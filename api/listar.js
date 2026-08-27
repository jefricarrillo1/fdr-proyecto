// GET /api/listar
// Devuelve todas las sugerencias con nombres de carrera/grado/sección.
const pool = require('./db');

module.exports = async function handler(req, res) {
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Método no permitido' });
  }

  const sql = `
    SELECT s.id_sugerencia, s.nombre_remitente, s.texto_sugerencia, s.fecha_creacion,
           c.nombre_carrera, g.nombre_grado, sec.nombre_seccion
    FROM sugerencias s
    INNER JOIN carreras c ON c.id_carrera = s.id_carrera
    INNER JOIN grados g ON g.id_grado = s.id_grado
    INNER JOIN secciones sec ON sec.id_seccion = s.id_seccion
    ORDER BY s.fecha_creacion DESC
  `;

  try {
    const [rows] = await pool.query(sql);
    const mapSec = { 'A': '1', 'B': '2', 'C': '3' };
    const clean = rows.map(r => ({
      ...r,
      nombre_seccion: mapSec[r.nombre_seccion] || r.nombre_seccion
    }));
    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    return res.status(200).json(clean);
  } catch (e) {
    return res.status(500).json({ error: 'Error de conexión: ' + e.message });
  }
};