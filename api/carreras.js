// GET /api/carreras
// Devuelve la lista de carreras.
const pool = require('./db');

module.exports = async function handler(req, res) {
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Método no permitido' });
  }

  try {
    const [rows] = await pool.query(
      'SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera'
    );
    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    return res.status(200).json(rows);
  } catch (e) {
    return res.status(500).json({ error: 'Error de conexión: ' + e.message });
  }
};