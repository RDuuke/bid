<?php

$dbconn = pg_connect("host=104.154.151.200 dbname=LivingLabTransaccional user=UserBDAnalitics password=123456")
or die ("No se pudo conectar: " . pg_last_error());


"
Select
  ca.idcupanexo3,
  v.fechainicio,
  ca.fecha,
  a.numerosolicitud,
  a.idprioridadatencion,
  a.enviado as sol_enviada,
  ca.idestadocupanexo3,
  ca.idusuariodireccionador, -- cambiar por usuario
  ca.fechadireccionamiento,
  ca.idusuariodevolucion, --cambiar por usuario
  ca.fechadevolucion,
  ti.descripcion as tipo_documento,
  p2.numerodocumento,
  p2.nombre1,
  p2.nombre2,
  p2.apellido1,
  p2.apellido2,
  p2.fechanacimiento,
  p2.sexo,
  p2.raza,
  p2.idcoberturasalud,
  eps.nombre as eps,
  regexp_replace(nc.subjetivo, E'[\\n\\r]+', ' ', 'g' ) as subjetivo,
  regexp_replace(nc.objetivo, E'[\\n\\r]+', ' ', 'g' ) as objetivo,
  regexp_replace(nc.analisis, E'[\\n\\r]+', ' ', 'g' ) as analisis,
  regexp_replace(nc.plan, E'[\\n\\r]+', ' ', 'g' ) as plan,
  cie10.codigo as cie10_codigo,
  cie10.descripcion as cie10_descripcion,
  s.nombresede as sede,
  c.nombre as ciudad,
  C.codigociudad,
  ca.idnotaclinicarespuesta,
  p3.idusuario,
  ca.idusuarioresidente,
  ca.fechaactualizacionusuarioresidente,
  md.nombre as motivo_devolucion,
  cup.codigo as cup_codigo,
  cup.descripcion as cup_descripcion,
  ca.fechaautorizacion,
  ca.numeroautorizacion,
  ca.idusuarioautorizador,
  ca.idestadoautorizacioncupanexo3
from (((((((((((((((visita as v
  join encuentro as e on v.idvisita = e.idvisita)
  join anexo3 as a on e.idencuentro = a.idanexo3)
  join cupanexo3 as ca on a.idanexo3 = ca.idanexo3)
  join paciente as p on v.idpaciente = p.idpaciente)
  join persona as p2 on p.idpersona = p2.idpersona)
  join notaclinica as nc on a.idnotaclinica = nc.idnotaclinica)
  join tipoidentificacion as ti on ti.idtipoidentificacion = p2.idtipodocumento)
  join cie10 on nc.idcie10principal = cie10.idcie10)
  join sedeinstitucion as s on e.idsede = s.idsede)
  join ciudad as c on s.idmunicipio = c.idciudad)
  left join departamento as d2 on c.iddepartamento = d2.iddepartamento)
  join prestador as p3 on e.idprestador = p3.idprestador)
  left join cup on ca.idcup = cup.idcup)
  left join eps on eps.ideps = p2.ideps)
  left join motivodevolucioncupanexo3 as md on md.idmotivodevolucioncupanexo3 = ca.idmotivodevolucioncupanexo3)
where ca.idestadocupanexo3 in ('1','2') and  nc.enviada  in ('true') and a.enviado in ('true')  and (not ca.idnotaclinicarespuesta is NULL);
    ";