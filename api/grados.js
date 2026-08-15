// GET /api/grados?carrera=ID
// Devuelve los grados de una carrera.
const pool = require('./db');

module.exports = async function handler(req, res) {
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Método no permitido' });
  }

  const carrera = Number(req.query.carrera);
  if (!carrera || carrera <= 0) {
    return res.status(200).json([]);
  }

  try {
    const [rows] = await pool.query(
      'SELECT id_grado, nombre_grado FROM grados WHERE id_carrera = ? ORDER BY id_grado',
      [carrera]
    );
    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    return res.status(200).json(rows);
  } catch (e) {
    return res.status(500).json({ error: 'Error de conexión: ' + e.message });
  }
};