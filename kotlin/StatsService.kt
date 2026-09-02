/**
 * ============================================================
 *  POSTRES LAURA — Componente Kotlin
 *  Calcula estadísticas de ventas y las expone como JSON
 *  para ser consumidas por el sistema Laravel vía API REST.
 *
 *  Interoperabilidad: Laravel llama a GET /kotlin/stats
 *  que devuelve métricas calculadas con lógica Kotlin.
 *
 *  Compilar : kotlinc StatsService.kt -include-runtime -d stats.jar
 *  Ejecutar : java -jar stats.jar
 * ============================================================
 */

import com.sun.net.httpserver.HttpServer
import java.io.PrintWriter
import java.net.InetSocketAddress
import java.sql.DriverManager
import java.time.LocalDate
import java.time.format.DateTimeFormatter

// ── Modelos de datos ──────────────────────────────────────
data class VentaStats(
    val mes: String,
    val totalVentas: Int,
    val ingresosMes: Double,
    val ticketPromedio: Double
)

data class ProductoTop(
    val descripcion: String,
    val veces: Int,
    val ingresos: Double
)

data class StatsResponse(
    val generadoEn: String,
    val ventasPorMes: List<VentaStats>,
    val topProductos: List<ProductoTop>,
    val totalIngresos: Double,
    val totalVentas: Int
)

// ── Servicio de estadísticas ──────────────────────────────
object StatsService {

    private const val DB_URL  = "jdbc:mysql://127.0.0.1:3306/postres?useSSL=false&serverTimezone=UTC"
    private const val DB_USER = "root"
    private const val DB_PASS = ""

    fun calcularStats(): StatsResponse {
        val ventasMes   = mutableListOf<VentaStats>()
        val topProductos = mutableListOf<ProductoTop>()
        var totalIngresos = 0.0
        var totalVentas   = 0

        DriverManager.getConnection(DB_URL, DB_USER, DB_PASS).use { conn ->

            // Ventas por mes
            conn.prepareStatement("""
                SELECT DATE_FORMAT(fecha,'%m-%Y') AS mes,
                       COUNT(*)      AS total_ventas,
                       SUM(total)    AS ingresos_mes,
                       AVG(total)    AS ticket_promedio
                FROM venta
                GROUP BY DATE_FORMAT(fecha,'%m-%Y')
                ORDER BY mes
            """).executeQuery().use { rs ->
                while (rs.next()) {
                    ventasMes.add(VentaStats(
                        mes           = rs.getString("mes"),
                        totalVentas   = rs.getInt("total_ventas"),
                        ingresosMes   = rs.getDouble("ingresos_mes"),
                        ticketPromedio = rs.getDouble("ticket_promedio")
                    ))
                }
            }

            // Top 5 productos
            conn.prepareStatement("""
                SELECT p.descripcion,
                       COUNT(v.id_venta) AS veces,
                       SUM(v.total)      AS ingresos
                FROM producto p
                JOIN venta v ON v.id_producto = p.id_producto
                GROUP BY p.id_producto
                ORDER BY veces DESC
                LIMIT 5
            """).executeQuery().use { rs ->
                while (rs.next()) {
                    topProductos.add(ProductoTop(
                        descripcion = rs.getString("descripcion"),
                        veces       = rs.getInt("veces"),
                        ingresos    = rs.getDouble("ingresos")
                    ))
                }
            }

            // Totales globales
            conn.prepareStatement("SELECT COUNT(*) AS c, COALESCE(SUM(total),0) AS s FROM venta")
                .executeQuery().use { rs ->
                    if (rs.next()) {
                        totalVentas   = rs.getInt("c")
                        totalIngresos = rs.getDouble("s")
                    }
                }
        }

        return StatsResponse(
            generadoEn   = LocalDate.now().format(DateTimeFormatter.ofPattern("dd/MM/yyyy")),
            ventasPorMes = ventasMes,
            topProductos = topProductos,
            totalIngresos = totalIngresos,
            totalVentas   = totalVentas
        )
    }
}

// ── Serialización JSON manual (sin dependencias externas) ──
fun StatsResponse.toJson(): String {
    fun VentaStats.toJson()    = """{"mes":"$mes","totalVentas":$totalVentas,"ingresosMes":$ingresosMes,"ticketPromedio":$ticketPromedio}"""
    fun ProductoTop.toJson()   = """{"descripcion":"$descripcion","veces":$veces,"ingresos":$ingresos}"""
    return """{
  "generadoEn":"$generadoEn",
  "totalVentas":$totalVentas,
  "totalIngresos":$totalIngresos,
  "ventasPorMes":[${ventasPorMes.joinToString(",") { it.toJson() }}],
  "topProductos":[${topProductos.joinToString(",") { it.toJson() }}]
}"""
}

// ── Servidor HTTP liviano en puerto 8081 ──────────────────
fun main() {
    Class.forName("com.mysql.cj.jdbc.Driver")
    val server = HttpServer.create(InetSocketAddress(8081), 0)

    server.createContext("/kotlin/stats") { exchange ->
        try {
            val json = StatsService.calcularStats().toJson()
            val bytes = json.toByteArray(Charsets.UTF_8)
            exchange.responseHeaders.add("Content-Type", "application/json; charset=UTF-8")
            exchange.responseHeaders.add("Access-Control-Allow-Origin", "*")
            exchange.sendResponseHeaders(200, bytes.size.toLong())
            exchange.responseBody.use { it.write(bytes) }
        } catch (e: Exception) {
            val err = """{"error":"${e.message}"}""".toByteArray()
            exchange.sendResponseHeaders(500, err.size.toLong())
            exchange.responseBody.use { it.write(err) }
        }
    }

    server.createContext("/kotlin/health") { exchange ->
        val body = """{"status":"ok","service":"postres-kotlin-stats"}""".toByteArray()
        exchange.responseHeaders.add("Content-Type", "application/json")
        exchange.sendResponseHeaders(200, body.size.toLong())
        exchange.responseBody.use { it.write(body) }
    }

    server.start()
    println("🍰 Postres Laura — Servicio Kotlin iniciado en http://localhost:8081")
    println("   GET /kotlin/stats  → estadísticas de ventas")
    println("   GET /kotlin/health → estado del servicio")
}
