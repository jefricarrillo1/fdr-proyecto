// =========================================================
// Conexión MySQL compartida (Vercel Serverless)
//
// Variables de entorno (configuradas en Vercel -> Settings ->
// Environment Variables):
//   DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME
//   DB_CA_CERT_PEM  (contenido del certificado ca.pem de Aiven)
//   DB_CA_CERT      (nombre alternativo si pegaste el cert ahí)
//   DB_REJECT_UNAUTHORIZED=true  (solo si quieres desactivar la
//   verificación SSL; NO recomendado)
// =========================================================

const mysql = require('mysql2/promise');

const ca =
  process.env.DB_CA_CERT_PEM ||
  process.env.DB_CA_CERT ||
  null;

const rejectUnauthorized =
  process.env.DB_REJECT_UNAUTHORIZED !== 'true';

const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  port: Number(process.env.DB_PORT || 3306),
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASS || '',
  database: process.env.DB_NAME || 'defaultdb',
  waitForConnections: true,
  connectionLimit: 5,
  ssl: ca ? { ca, rejectUnauthorized } : {},
});

module.exports = pool;