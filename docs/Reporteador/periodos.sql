-- ----------------------------
-- Table structure for cod_periodos
-- ----------------------------
DROP TABLE IF EXISTS "public"."cod_periodos";
CREATE TABLE "public"."cod_periodos" (
  "descripcion" varchar(80),
  "frecuencia" int4 NOT NULL,
  "ident" int4 NOT NULL,
  "creado" timestamp(6) NOT NULL DEFAULT now(),
  "modificado" timestamp(6) NOT NULL DEFAULT now(),
  "importado" bool,
  "id" int4 NOT NULL DEFAULT nextval('cod_periodos_id_seq'::regclass)
)
;
COMMENT ON COLUMN "public"."cod_periodos"."descripcion" IS 'Puede ser mes actual, mes anterior, año actual, año anterior, 1er semestre.... Es una descripción definida por el usuario.';
COMMENT ON COLUMN "public"."cod_periodos"."frecuencia" IS '1 para día, 2 para mes, 3 para año.';
COMMENT ON COLUMN "public"."cod_periodos"."ident" IS 'Identificador de los períodos.';
COMMENT ON COLUMN "public"."cod_periodos"."creado" IS 'Fecha y Hora de creado el registro.
';
COMMENT ON COLUMN "public"."cod_periodos"."modificado" IS 'Fecha y Hora de la última modificación del registro.';
COMMENT ON COLUMN "public"."cod_periodos"."importado" IS 'Define si ha sido importado el registro.';
COMMENT ON TABLE "public"."cod_periodos" IS 'Períodos para los reportes y muestra la información.';

-- ----------------------------
-- Records of cod_periodos
-- ----------------------------
INSERT INTO "public"."cod_periodos" VALUES ('Selección', 1, 49, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 1);
INSERT INTO "public"."cod_periodos" VALUES ('Selección', 2, 50, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 2);
INSERT INTO "public"."cod_periodos" VALUES ('Selección', 3, 51, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 3);
INSERT INTO "public"."cod_periodos" VALUES ('Último día del mes', 1, 41, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 4);
INSERT INTO "public"."cod_periodos" VALUES ('Días atrás', 1, 32, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 5);
INSERT INTO "public"."cod_periodos" VALUES ('Día', 1, 33, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 6);
INSERT INTO "public"."cod_periodos" VALUES ('Meses atrás', 2, 34, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 7);
INSERT INTO "public"."cod_periodos" VALUES ('Mes', 2, 35, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 8);
INSERT INTO "public"."cod_periodos" VALUES ('Años atrás', 3, 36, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 9);
INSERT INTO "public"."cod_periodos" VALUES ('Primer día del mes', 1, 40, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 10);
INSERT INTO "public"."cod_periodos" VALUES ('Año', 3, 37, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 11);
INSERT INTO "public"."cod_periodos" VALUES ('Día actual', 1, 0, 'Sat 27 Aug 14:42:00 2011', 'Wed 21 Sep 09:37:37.256 2011', 'f', 12);
INSERT INTO "public"."cod_periodos" VALUES ('Mes actual', 2, 0, 'Sat 27 Aug 14:42:00 2011', 'Wed 21 Sep 09:37:42.35 2011', 'f', 13);
INSERT INTO "public"."cod_periodos" VALUES ('Año actual', 3, 0, 'Sat 27 Aug 14:42:00 2011', 'Wed 21 Sep 09:37:47.538 2011', 'f', 14);
INSERT INTO "public"."cod_periodos" VALUES ('Selección', 1, 49, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 15);
INSERT INTO "public"."cod_periodos" VALUES ('Selección', 2, 50, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 16);
INSERT INTO "public"."cod_periodos" VALUES ('Selección', 3, 51, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 17);
INSERT INTO "public"."cod_periodos" VALUES ('Último día del mes', 1, 41, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 18);
INSERT INTO "public"."cod_periodos" VALUES ('Días atrás', 1, 32, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 19);
INSERT INTO "public"."cod_periodos" VALUES ('Día', 1, 33, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 20);
INSERT INTO "public"."cod_periodos" VALUES ('Meses atrás', 2, 34, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 21);
INSERT INTO "public"."cod_periodos" VALUES ('Mes', 2, 35, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 22);
INSERT INTO "public"."cod_periodos" VALUES ('Años atrás', 3, 36, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 23);
INSERT INTO "public"."cod_periodos" VALUES ('Primer día del mes', 1, 40, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 24);
INSERT INTO "public"."cod_periodos" VALUES ('Año', 3, 37, 'Sat 27 Aug 14:42:00 2011', 'Sat 27 Aug 14:42:00 2011', 'f', 25);
INSERT INTO "public"."cod_periodos" VALUES ('Día actual', 1, 0, 'Sat 27 Aug 14:42:00 2011', 'Wed 21 Sep 09:37:37.256 2011', 'f', 26);
INSERT INTO "public"."cod_periodos" VALUES ('Mes actual', 2, 0, 'Sat 27 Aug 14:42:00 2011', 'Wed 21 Sep 09:37:42.35 2011', 'f', 27);
INSERT INTO "public"."cod_periodos" VALUES ('Año actual', 3, 0, 'Sat 27 Aug 14:42:00 2011', 'Wed 21 Sep 09:37:47.538 2011', 'f', 28);
INSERT INTO "public"."cod_periodos" VALUES ('Mes Ant. al Selecc.', 2, 52, 'Mon 14 Jul 10:15:12 2014', 'Fri 13 Mar 08:05:56.91 2015', 'f', 29);
INSERT INTO "public"."cod_periodos" VALUES ('Mes Ant. al Selecc.', 2, 52, 'Mon 14 Jul 10:15:44 2014', 'Fri 13 Mar 08:06:02.547 2015', 'f', 30);
INSERT INTO "public"."cod_periodos" VALUES ('D&iacutea Ant. al Ini. Selec.', 1, 53, 'Thu 04 Aug 19:09:17 2016', 'Thu 04 Aug 19:09:40 2016', 'f', 31);
INSERT INTO "public"."cod_periodos" VALUES ('D&iacutea Post. al Ult. Selec.', 1, 54, 'Thu 04 Aug 19:09:36 2016', 'Thu 04 Aug 19:12:08 2016', 'f', 32);

-- ----------------------------
-- Table structure for periodos
-- ----------------------------
DROP TABLE IF EXISTS "public"."periodos";
CREATE TABLE "public"."periodos" (
  "id" varchar(32) NOT NULL DEFAULT replace(((uuid_generate_v4())::character varying)::text, '-'::text, ''::text),
  "ident" varchar(30) NOT NULL,
  "descripcion" varchar(80),
  "diai" int2,
  "mesi" int2,
  "anoi" int4,
  "diaf" int2,
  "mesf" int2,
  "anof" int4,
  "creado" timestamptz(6) DEFAULT now(),
  "modificado" timestamptz(6) DEFAULT now(),
  "importado" bool
)
;
COMMENT ON COLUMN "public"."periodos"."id" IS 'Identificador del período, es único para cada período, autoincremental.';
COMMENT ON COLUMN "public"."periodos"."ident" IS 'Descripción del período. Puede ser año actual, mes actual, año anterio, mes anterior....Es un descriptor definido por el usuario.';
COMMENT ON COLUMN "public"."periodos"."descripcion" IS 'Descripción detallada de este período.';
COMMENT ON COLUMN "public"."periodos"."diai" IS 'Identificador para día inicial.';
COMMENT ON COLUMN "public"."periodos"."mesi" IS 'Identificador para mes inicial.';
COMMENT ON COLUMN "public"."periodos"."anoi" IS 'Identificador para año inicial.';
COMMENT ON COLUMN "public"."periodos"."diaf" IS 'Identificador para día final.';
COMMENT ON COLUMN "public"."periodos"."mesf" IS 'Identificador para mes final.';
COMMENT ON COLUMN "public"."periodos"."anof" IS 'Identificador para año final.';
COMMENT ON TABLE "public"."periodos" IS 'Se almacenan los períodos para los reportes y muestra la información aún en análisis.';

-- ----------------------------
-- Records of periodos
-- ----------------------------
INSERT INTO "public"."periodos" VALUES ('8ef0223e4b054d8abd49ff7ea3f5fad4', 'Día Ant al Selec.- Hasta Selec', 'Día Anterior a la Fecha Seleccionada', 53, 50, 51, 53, 50, 51, 'Thu 04 Aug 19:28:59 2016 COT', 'Thu 04 Aug 19:28:59 2016 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2f685cb76faa44fc9c79cc53f9c31539', 'Julio', '', 1, 7, 51, 31, 7, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7e3ea0595f664977bf9459750ad92a95', 'Junio', '', 1, 6, 51, 30, 6, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('0dc285a441214a808e92db084be60aa9', 'Marzo', '', 1, 3, 51, 31, 3, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('d38d583d75984f0e99b3edd9d69dfd48', 'Mayo', '', 1, 5, 51, 31, 5, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('39ad44aefcea4ca0b0d092e4fabe560a', 'Noviembre', '', 1, 11, 51, 30, 11, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('5ebe81eb8cb64d36bcc19c59d5c509d6', 'Octubre', '', 1, 10, 51, 31, 10, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2055f76e497f4c2b84bdfea777fe7773', 'Septiembre', '', 1, 9, 51, 30, 9, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('ef7320901e5141a3a5fff87831632504', 'Agosto', '', 1, 8, 51, 31, 8, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('cd4cf53ba38d455c83089cd297f505ad', 'Diciembre', '', 1, 12, 51, 31, 12, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7e65dd8e09d54846adeb4cb305b6f8b7', '1ra. Dec.', '1ra Decena', 40, 50, 51, 10, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('1494995365864ed084e71a0795862cbd', 'Enero', 'Enero', 1, 1, 51, 31, 1, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2d47f73aa1b64a9d8787764d9f1ed242', '1ra. Quinc.', '1ra Quincena', 40, 50, 51, 15, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('f7e4545a4ae54cfc855032faf697e902', '2da. Quinc.', '2da Quincena', 16, 50, 51, 41, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('ff2b2ca3ef9f4c64a2e3523200711755', '2da. Dec.', '2da Decena', 11, 50, 51, 20, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('85e7d5cb9ce44ef4a032a2e47db4cce2', '2do. Semestre', '2do Semestre', 1, 7, 51, 41, 12, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('0bed29423f19439cb702b5043c4fc0d0', '3ra. Dec.', '3ra Decena', 21, 50, 51, 41, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('d0001819f386489694054f6d0a511582', '1er. Semestres', '1er Semestre', 40, 1, 51, 41, 6, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('5c219493c7ab441fb9336853ea93d783', 'Abril', '', 1, 4, 51, 30, 4, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('4c288fd8c4054c6581157030917dc399', '1', 'Día 1 del Mes y Año Seleccionado', 1, 50, 51, 1, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('98bab5ebdac3423ea0928ab8e8480d66', '2', 'Día 2 del Mes y Año Seleccionado', 2, 50, 51, 2, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('3ff5d207be524d4abb5d6c31d3532667', '4', 'Día 4 del Mes y Año Seleccionado', 4, 50, 51, 4, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('01f98a852ea347a98b109544a87d339c', '5', 'Día 5 del Mes y Año Seleccionado', 5, 50, 51, 5, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('4a88641784ab444c91f3f571a8f087c2', 'Año HF', 'Año Actual Hasta Fecha', 1, 1, 0, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('34a80f3be0cf4eb38e9d4456ebe10e28', 'Cp. Tab. Acopio', 'Campaña de Acopio de Tabaco', 40, 2, 0, 41, 9, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('6b70427617474748b6c6c3405f7bcafb', 'Cp. Tab. Contrat. 16/17', 'Contrataci?e la camapa?6/17', 1, 3, 2015, 41, 8, 2015, 'Thu 27 Aug 10:23:28 2015 COT', 'Thu 27 Aug 10:23:28 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('db0d54349c384aa785028fd5460f7928', 'Mes HF', 'Mes hasta fecha', 1, 0, 0, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('094ca11ed2d64883a8d08fb33162d07e', 'Cp. Tab. Bal Post 14', 'Campaña de Balance de Posturas del tabaco 2012-2013', 40, 9, 2014, 41, 3, 2015, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('4b0e4a0cc8b34352a4d6bc9f28f30cd7', 'Cp. Tab. Bal Post 15', 'Campaña de Balance de Posturas del Tabaco Año 2013-2014', 40, 9, 2015, 41, 3, 2016, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('19ac15efaa1047d9b8a4eab388376853', 'Cp. Tab. Contrat. 13/14', 'Contratación de Tabaco Campaña 2013/2014', 40, 1, 2014, 41, 10, 2014, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('4f838cb5a12a4d60946393cba00651db', '20', 'Día 20 del Mes y Año Seleccionado', 20, 50, 51, 20, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('41a2073706964d2387aa5a47e194b182', '29', 'Día 29 del Mes y Año Seleccionado', 29, 50, 51, 29, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('50281992ec21454eb256e0aa4e29d5db', '30', 'Día 30 del Mes y Año Seleccionado', 30, 50, 51, 30, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7a3d338ba0ff487485c01d4344329fcd', '31', 'Día 31 del Mes y Año Seleccionado', 31, 50, 51, 31, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7f334be3f7604fb59acea1c6f8a5ee27', 'Mes Ant.', 'Mes Anterior', 40, -1, 0, 41, -1, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('407427ac09e344b4804c5e53236b4245', 'Cp. Tab. PS Plant HF', 'Campaña Hasta Fecha de Preparación de Suelos para la Plantación de Tabaco', 40, 6, 2014, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7d50266988454f03986d24fe9a371111', '3', 'Día 3 del Mes y Año Seleccionado', 3, 50, 51, 3, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('071ca306f6114513942a79fd84b8fc10', '6', 'Día 6 del Mes y Año Seleccionado', 6, 50, 51, 6, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('3580ef79148a459c91c82cca27d9cb59', '12', 'Día 12 del Mes y Año Seleccionado', 12, 50, 51, 12, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('ca165e76d85e406798263b489277b293', '13', 'Día 13 del Mes y Año Seleccionado', 13, 50, 51, 13, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('53e4362450fc4b04930c5cded10c8744', '14', 'Día 14 del Mes y Año Seleccionado', 14, 50, 51, 14, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('1eff3f80013b4c0d9c166d24d9474de7', '15', 'Día 15 del Mes y Año Seleccionado', 15, 50, 51, 15, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('adb8d9cb9c5c4217ac80e89dd5b1feda', '16', 'Día 16 del Mes y Año Seleccionado', 16, 50, 51, 16, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('9856ee2833a54799b3d1cb62bfb1420d', '17', 'Día 17 del Mes y Año Seleccionado', 17, 50, 51, 17, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('6c92918828a34a3aa4113de7564a3983', '18', 'Día 18 del Mes y Año Seleccionado', 18, 50, 51, 18, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2774593f683e4de7a946947184f1522f', '19', 'Día 19 del Mes y Año Seleccionado', 19, 50, 51, 19, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('b66ead256f6047ffa6caeb481ac8d532', '21', 'Día 21 del Mes y Año Seleccionado', 21, 50, 51, 21, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7a9a0962c0a64508a00a5b2777fc210e', '22', 'Día 22 del Mes y Año Seleccionado', 22, 50, 51, 22, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('c9394a06f14d4eaf8c89fae5bb6bf3b1', '23', 'Día 23 del Mes y Año Seleccionado', 23, 50, 51, 23, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7dd53a8b5cae4a9ea03d972097fea290', '24', 'Día 24 del Mes y Año Seleccionado', 24, 50, 51, 24, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('997c362f491748cabaa426f1fb43d6b0', '25', 'Día 25 del Mes y Año Seleccionado', 25, 50, 51, 25, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2becf2a09d204db09c95a211f06590ea', '26', 'Día 26 del Mes y Año Seleccionado', 26, 50, 51, 26, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('397aa3e0a2aa4679baa8fd0cc8065985', '27', 'Día 27 del Mes y Año Seleccionado', 27, 50, 51, 27, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('a8809fcdbff74e7a8246df5d1385c5eb', '28', 'Día 28 del Mes y Año Seleccionado', 28, 50, 51, 28, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('5357acda9438454d9cec1604adf81b9e', '8', 'Día 8 del Mes y Año Seleccionado', 8, 50, 51, 8, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('d814053ce81a4ca395e2dce8e95d3a8a', '9', 'Día 9 del Mes y Año Seleccionado', 9, 50, 51, 9, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('c6be06cbcc5143db9649b68a3e29709e', '10', 'Día 10 del Mes y Año Seleccionado', 10, 50, 51, 10, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('cf3864039a0a415ab4724004a3932ea8', 'Cp. Frijol  Frío HF', 'Campaña de Frijol de Frío Hasta Fecha', 40, 9, 2015, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('3b72be2c86e04104a209d91902b2213e', 'Cp. Frijol  Prim HF', 'Campaña de Frijol de Primavera  Hasta Fecha', 40, 3, 2015, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('c424773c84d6454db238059a815ef9d6', 'Selección', 'Rango de fechas seleccionadas al generar el informe', 49, 50, 51, 49, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('d9dac7d48d2747468ab24fe2d1a6d707', 'Año Ant. HF.', 'Año anterior hasta fecha', 1, 1, -1, 0, 0, -1, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('a01edfa910384ce3b0747a1de3c3361c', 'Hoy', 'Día actual', 0, 0, 0, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2b762529f90a4ed884e522e60980cdaf', 'Año Ant.', 'Año anterior', 1, 1, -1, 41, 12, -1, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('aa83bb82cce14ae7ad8344aadab5e658', 'Hasta Fin Mes Actual', 'Hasta el fin del mes actual', 1, 1, 51, 41, 0, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('7c62e2e567d04a38b55f2d8f312e3fa9', 'Hasta Fin Mes Ant.', 'Hasta el Fin del Mes Anterior', 40, 1, 51, 41, -1, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('fdacc38d6a0840128ac56e03e8ee6161', 'Hasta Fin Mes Selecc', 'Desde el 1ro de Enero hasta el Fin del Mes Seleccioando', 1, 1, 51, 41, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('784f012915b648b4ba387ed0d8128354', '1er Trimestre', 'Primer Trimestre', 40, 1, 51, 41, 3, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('3674500858d54f2fbbc45abd29ee2c04', '2do Trimestre', 'Segundo Trimestre', 40, 4, 51, 41, 6, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('52abcea80090477187c94687247c40e6', 'Hasta Fin Mes Ant. al Selecc.', 'Hasta el fin del mes anterior al seleccionado', 40, 1, 51, 41, 52, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('8595e2af8c5c48eda261da45409971a2', '3er Trimestre', 'Tercer Trimestre', 40, 7, 51, 41, 9, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('cf3be13cbd464d21beb5a4bfa7e23c59', '4to Trimestre', 'Cuarto Trimestre', 40, 10, 51, 41, 12, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('6fd789edf2fc4957b9e6732629df5c0e', 'Cp. Tab. Contrat. 14/15', 'Contratación de Tabaco Campaña 2014/2015', 40, 11, 2014, 41, 11, 2014, 'Sun 01 Mar 18:06:36.002 2015 COT', 'Sun 01 Mar 18:06:36.002 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('ade08fb386a5461eaad5fe0301ab7c3c', 'Mes Ant. Selecc.', 'Mes anterior al seleccionado', 40, 52, 51, 41, 52, 51, 'Wed 11 Mar 12:31:51.719 2015 COT', 'Wed 11 Mar 12:31:51.719 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('5789c212411044b9ba0c6639f5afd943', '1er Día Año Seleccionado', 'Primer día del año seleccionado', 1, 1, 51, 1, 1, 51, 'Fri 13 Mar 08:13:40.582 2015 COT', 'Fri 13 Mar 08:13:40.582 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('81c1bf19082d4d20b6b17b35a883bed3', 'Hasta Día Seleccionado', 'Del pimer día del año hasta el día seleccionado', 1, 1, 51, 49, 50, 51, 'Thu 12 Mar 20:20:40.114 2015 COT', 'Thu 12 Mar 20:20:40.114 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('b792977cf70948da8fcad854fa02ce49', 'Cp. Tab. Contrat. 15/16', 'Contrataci?e Tabaco Campa?5/16', 1, 12, 2014, 41, 2, 2015, 'Sun 01 Mar 18:08:41.934 2015 COT', 'Sun 01 Mar 18:08:41.934 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('d2b356b8e97d4eb4846b53a274eb19f2', 'Mes', 'Mes actual', 1, 0, 0, 41, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2f2275221e084d61afb3a31b8124d27a', '7', 'Día 7 del Mes y Año Seleccionado', 7, 50, 51, 7, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('e273da589bbe4c06b9551294ce9eb089', 'Año', 'Año actual', 1, 1, 0, 41, 12, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('fe6cf80cd9f3476994f8996c46ec5803', 'Semana Ant.', 'Semana Anterior', -8, 0, 0, -1, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('3583a1bf29e444c9bbd70563dca5494e', 'Ayer', 'Día anterior', -1, 0, 0, -1, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('465f7781d5de4fc78a76aa77b7eaa97c', '2da. Prep. Suelo P.', '2da Etapa de Preparación de Suelo para Plantación', 40, 9, 2013, 41, 1, 2014, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('18eac604f5274b6fb6588218b4ea94de', '11', 'Día 11 del Mes y Año Seleccionado', 11, 50, 51, 11, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('b6de08802f4e471db8eaa38b251ae3d9', 'Cp. Frijol Prim', 'Campaña de Frijol de Primavera ', 40, 3, 2015, 41, 8, 2015, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('3626f0855e2b4315b55b9a0b7ea05071', 'Cp. Tab. Estimado Acopio', 'Campaña de Estimado de Acopio de Tabaco', 40, 5, 51, 41, 6, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('bb328da92da04000acbe95978fc85870', 'Cp. Tab. Prep Sem HF', 'Campaña Hasta Fecha de Preparación de Semilleros', 40, 5, 51, 49, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('61adcb2b930a42d1b9cdd041907ffe7c', 'Cp. Tab. Prep. Sem.', 'Preparación de Semillero de Tabaco', 40, 5, 51, 41, 12, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('6409777d80a34813a567e71ef8f7dace', 'Todo el Tiempo', 'Esto se utiliza para abarcar todo el tiempo de la Campaña', 1, 1, 2010, 31, 12, 2100, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('52a43efc4b494e8fb69efcbe8184593f', 'Cp. Tab. Zafadura', 'Campaña de Zafadura del Tabaco', 40, 2, 0, 41, 7, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('dc8360d039b5448ba777aac8293a05a5', 'Cp. Tab. Zafadura HF', 'Campaña Hasta Fecha de Zafadura', 40, 2, 0, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('2b0cb1452cc24b749d3fad33ab04413d', 'Cp. Tab. Acopio HF', 'Campaña Hasta Fecha de Acopio de Tabaco', 40, 2, 0, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('6e894e25edfe4e6891c7ca9f22c298c8', 'Zafra 2013-2014', 'Zafra 2013-2014', 1, 9, 2013, 41, 6, 2014, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('28b9c30966dd4e1a8ff43dbedf90df6b', 'Zafra 2013-2014 HF', 'Zafra 2013-2014 HF', 1, 9, 2013, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('79374c6e26484492b490e065f2f035b7', 'Mes Selección', 'Mes seleccionado', 40, 50, 51, 41, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('02c33f94b895434c954cb74338508cf5', 'Mes Selección HF', 'Mes seleccionado hasta fecha', 40, 50, 51, 0, 50, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('c84aaf4cb09b4f42b6b18170061db0ec', 'Cp. Tab. PS Plant', 'Preparación de Suelo para Plantación de Tabaco', 40, 6, 2014, 41, 1, 2015, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('8395de9781a0466a93a99fe89f026c2b', 'Año Selecc. HF.', 'Año hasta fecha seún selección', 40, 1, 51, 0, 0, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('0136ee8673c94ebb947f88dd97dd0c58', 'Año Seleccionado', 'Año según año de selección', 40, 1, 51, 41, 12, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('0ca33115a1ee4d25ad8312107678d702', 'Febrero', 'Febrero', 1, 2, 51, 41, 2, 51, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('e99530767aa14e27a6ccea4915057e7d', 'Cp. Frijol. Frío', 'Campaña de Frijol de Frío', 40, 9, 2015, 41, 2, 2016, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('bfc836446f0e4f0a9baba45ca27f86f8', 'Cp. Tab. Cosecha', 'Campaña Actual de Cosecha de Tabaco', 40, 11, 2015, 41, 4, 2016, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('e4e1257f78eb44da9d43b6e913b69565', 'Cp. Tab. Cosecha HF', 'Campaña de Cosecha Hasta Fecha de Tabaco', 40, 11, 2015, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('f1d3b6000e8f4d7e80d10596646abf30', 'Cp. Tb. Siemb. ', 'Campaña de Siembra de Tabaco Actual', 40, 9, 2015, 41, 2, 2016, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('f9301a89a9f545e1895b7744761a047c', 'Cp. Tb. Siemb. HF', 'Campaña de Siembra de Tabaco Actual Hasta Fecha', 40, 9, 2015, 0, 0, 0, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');
INSERT INTO "public"."periodos" VALUES ('437120dd4a65432ca2a8d01ed6c26593', 'Año Próximo', 'Año Siguiente', 1, 1, 2016, 41, 12, 2016, 'Thu 01 Jan 00:59:59 2015 COT', 'Thu 01 Jan 00:59:59 2015 COT', 't');

-- ----------------------------
-- Primary Key structure for table cod_periodos
-- ----------------------------
ALTER TABLE "public"."cod_periodos" ADD CONSTRAINT "cod_periodos_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table periodos
-- ----------------------------
ALTER TABLE "public"."periodos" ADD CONSTRAINT "pkperiodos" PRIMARY KEY ("id");
