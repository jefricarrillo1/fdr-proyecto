// POST /api/guardar
// Inserta una sugerencia en la tabla "sugerencias".
// Recibe: nombre, carrera, grado, seccion, texto (JSON o form-urlencoded).
const pool = require('./db');

module.exports = async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Método no permitido' });
  }

  const body = req.body || {};

  const nombre =
    String(body.nombre || '').trim();
  const idCarrera = Number(body.carrera);
  const idGrado = Number(body.grado);
  const idSeccion = Number(body.seccion);
  const texto =
    String(body.texto || '').trim();

  if (!nombre || !idCarrera || !idGrado || !idSeccion || !texto) {
    return res.status(400).json({ error: 'Todos los campos son obligatorios.' });
  }

  try {
    const [result] = await pool.query(
      'INSERT INTO sugerencias (nombre_remitente, id_carrera, id_grado, id_seccion, texto_sugerencia) VALUES (?, ?, ?, ?, ?)',
      [nombre, idCarrera, idGrado, idSeccion, texto]
    );

    // Nombres para la respuesta
    const [c] = await pool.query('SELECT nombre_carrera FROM carreras WHERE id_carrera = ?', [idCarrera]);
    const [g] = await pool.query('SELECT nombre_grado FROM grados WHERE id_grado = ?', [idGrado]);
    const [s] = await pool.query('SELECT nombre_seccion FROM secciones WHERE id_seccion = ?', [idSeccion]);

    let nombreCarrera = c[0] ? c[0].nombre_carrera : '';
    nombreCarrera = nombreCarrera.replace(/^Bachillerato en\s*/i, '').replace(/^Bach\.\s*/i, '').trim();
    if (/maritimo portuario/i.test(nombreCarrera)) nombreCarrera = 'Marítimo Portuario';
    if (nombreCarrera.toLowerCase() === 'administración de empresas') nombreCarrera = 'Administración de Empresas';

    res.setHeader('Content-Type', 'application/json; charset=utf-8');
    return res.status(200).json({
      ok: true,
      id: result.insertId,
      nombre,
      carrera: nombreCarrera,
      grado: g[0] ? g[0].nombre_grado : '',
      seccion: s[0] ? s[0].nombre_seccion : ''
    });
  } catch (e) {
    return res.status(500).json({ error: 'Error al guardar la sugerencia: ' + e.message });
  }
};