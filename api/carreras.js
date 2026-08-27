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
    // Limpieza de nombres para mostrar sin prefijo Bachillerato/Bach.
    const clean = rows.map(r => {
      let n = r.nombre_carrera
        .replace(/^Bachillerato en\s*/i, '')
        .replace(/^Bach\.\s*/i, '')
        .trim();
      // Normalizar Marítimo Portuario
      if (/maritimo portuario/i.test(n)) n = 'Marítimo Portuario';
      // Capitalizar correctamente
      if (n.toLowerCase() === 'administración de empresas') n = 'Administración de Empresas';
      return { ...r, nombre_carrera: n };
    });
    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    return res.status(200).json(clean);
  } catch (e) {
    return res.status(500).json({ error: 'Error de conexión: ' + e.message });
  }
};