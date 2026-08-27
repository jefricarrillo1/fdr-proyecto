// GET /api/secciones
// Devuelve la lista de secciones.
const pool = require('./db');

module.exports = async function handler(req, res) {
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Método no permitido' });
  }

  try {
    const [rows] = await pool.query(
      'SELECT id_seccion, nombre_seccion FROM secciones ORDER BY nombre_seccion'
    );
    // Mapear A,B,C -> 1,2,3 por compatibilidad si la BD aún tiene letras
    const map = { 'A': '1', 'B': '2', 'C': '3' };
    const clean = rows.map(r => ({
      ...r,
      nombre_seccion: map[r.nombre_seccion] || r.nombre_seccion
    }));
    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    return res.status(200).json(clean);
  } catch (e) {
    return res.status(500).json({ error: 'Error de conexión: ' + e.message });
  }
};