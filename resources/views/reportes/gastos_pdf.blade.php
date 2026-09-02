<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Gastos — Postres Laura</title>
<style>
  body { font-family: DejaVu Sans, sans-serif; color: #2a1506; font-size: 11px; margin: 0; padding: 20px; }
  table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  th { background: #c9717a; color: white; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
  td { padding: 7px 10px; border-bottom: 1px solid #e8d8c8; }
  tr:nth-child(even) td { background: #fdf8f2; }
  .total-row td { background: #c0392b; color: white; font-weight: bold; font-size: 12px; }
  .header-box { background: #c9717a; color: white; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
  .logo { font-size: 22px; font-weight: 900; }
  .logo em { color: #fdf8f2; font-style: normal; }
  .chip { background: rgba(255,255,255,.2); padding: 3px 8px; border-radius: 20px; font-size: 9px; }
</style>
</head>
<body>
<div class="header-box">
  <div>
    <div class="logo">🍰 Postres<em>Laura</em></div>
    <div style="font-size:10px;color:rgba(255,255,255,.8)">Generado el {{ now()->format('d/m/Y H:i') }}</div>
  </div>
  <div style="text-align:right">
    <div style="font-size:14px;font-weight:700">REPORTE DE GASTOS</div>
    <div style="font-size:10px;color:rgba(255,255,255,.7)">SENA 228118 — Sistema de Gestión</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>Fecha</th><th>Descripción</th><th>Categoría</th><th>Monto</th><th>Presupuesto</th>
    </tr>
  </thead>
  <tbody>
    @foreach($gastos as $g)
    <tr>
      <td>{{ $g->fecha }}</td>
      <td>{{ $g->descripcion }}</td>
      <td><span class="chip">{{ $g->categoria }}</span></td>
      <td style="color:#c0392b"><strong>-${{ number_format($g->monto,0,'.','.') }}</strong></td>
      <td>${{ number_format($g->presupuesto,0,'.','.') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="3" style="text-align:right">TOTAL GASTOS</td>
      <td colspan="2">-${{ number_format($totalGastos,0,'.','.') }}</td>
    </tr>
  </tbody>
</table>

<p style="margin-top:30px;color:#8a6a50;font-size:9px;text-align:center">
  Postres Laura © {{ now()->year }} · Sistema de Gestión SENA 228118 · Documento generado automáticamente
</p>
</body>
</html>
