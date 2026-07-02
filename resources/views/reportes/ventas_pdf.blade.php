<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Ventas — Postres Laura</title>
<style>
  body { font-family: DejaVu Sans, sans-serif; color: #2a1506; font-size: 11px; margin: 0; padding: 20px; }
  h1 { font-size: 20px; color: #3d1f0a; margin-bottom: 4px; }
  .sub { color: #8a6a50; font-size: 10px; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  th { background: #3d1f0a; color: white; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
  td { padding: 7px 10px; border-bottom: 1px solid #e8d8c8; }
  tr:nth-child(even) td { background: #fdf8f2; }
  .total-row td { background: #c8773a; color: white; font-weight: bold; font-size: 12px; }
  .header-box { background: #3d1f0a; color: white; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
  .logo { font-size: 22px; font-weight: 900; }
  .logo em { color: #e8a84c; font-style: normal; }
  .fecha { font-size: 10px; color: rgba(255,255,255,.7); }
</style>
</head>
<body>
<div class="header-box">
  <div>
    <div class="logo">🍰 Postres<em>Laura</em></div>
    <div class="fecha">Reporte generado el {{ now()->format('d/m/Y H:i') }}</div>
  </div>
  <div style="text-align:right">
    <div style="font-size:14px;font-weight:700">REPORTE DE VENTAS</div>
    <div style="font-size:10px;color:rgba(255,255,255,.7)">SENA 228118 — Sistema de Gestión</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th><th>Fecha</th><th>Empleado</th><th>Producto</th><th>Total</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ventas as $v)
    <tr>
      <td>{{ $v->id_venta }}</td>
      <td>{{ $v->fecha }}</td>
      <td>{{ $v->empleado }}</td>
      <td>{{ $v->producto }}</td>
      <td><strong>${{ number_format($v->total,0,'.','.') }}</strong></td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="4" style="text-align:right">TOTAL GENERAL</td>
      <td>${{ number_format($totalVentas,0,'.','.') }}</td>
    </tr>
  </tbody>
</table>

<p style="margin-top:30px;color:#8a6a50;font-size:9px;text-align:center">
  Postres Laura © {{ now()->year }} · Sistema de Gestión SENA 228118 · Documento generado automáticamente
</p>
</body>
</html>
