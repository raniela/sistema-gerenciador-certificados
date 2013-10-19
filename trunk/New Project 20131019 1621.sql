-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.5.25a


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema sgc
--

CREATE DATABASE IF NOT EXISTS sgc;
USE sgc;

--
-- Definition of table `aluno`
--

DROP TABLE IF EXISTS `aluno`;
CREATE TABLE `aluno` (
  `id_aluno` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cliente` bigint(20) NOT NULL,
  `id_usuario` bigint(20) unsigned NOT NULL,
  `tx_nome_aluno` varchar(45) COLLATE utf8_bin NOT NULL,
  `tx_rg` varchar(9) COLLATE utf8_bin DEFAULT NULL,
  `tx_cpf` varchar(14) COLLATE utf8_bin NOT NULL,
  `tx_cargo` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_aluno`),
  KEY `fk_aluno_cliente` (`id_cliente`),
  KEY `FK_aluno_usuario` (`id_usuario`),
  CONSTRAINT `fk_aluno_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  CONSTRAINT `FK_aluno_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela responsável por armazenar os alunos que participam de algum treinamento da consultoria.';

--
-- Dumping data for table `aluno`
--

/*!40000 ALTER TABLE `aluno` DISABLE KEYS */;
INSERT INTO `aluno` (`id_aluno`,`id_cliente`,`id_usuario`,`tx_nome_aluno`,`tx_rg`,`tx_cpf`,`tx_cargo`) VALUES 
 (1,14,21,0x52616E69656C61205269636172646F20456469C3A7C3A36F,0x323331323332313332,0x3135362E3831332E3336332D3434,0x416E616C697374612064652053697374656D6173),
 (2,15,23,0x4D6172696120646173204E65766573,0x333435343331323334,0x3235392E3736322E3237312D3132,0x4D6573747265206465204F62726173),
 (3,15,24,0x506564726F204D656E656775656C,0x323336383534353331,0x3436372E3334372E3131352D3630,0x456D70726569746569726F),
 (4,15,26,0x4A6FC3A36F2064612053696C766120536175726F20456469C3A7C3A36F,0x323334353738353633,0x3339342E3138302E3838382D3531,0x54657374C3A36F20646F73204163656E746F73);
/*!40000 ALTER TABLE `aluno` ENABLE KEYS */;


--
-- Definition of table `certificado`
--

DROP TABLE IF EXISTS `certificado`;
CREATE TABLE `certificado` (
  `id_certificado` bigint(20) NOT NULL AUTO_INCREMENT,
  `tx_nome_modelo` varchar(30) COLLATE utf8_bin NOT NULL,
  `tx_url_logotipo_cabecalho_esquerda` varchar(37) COLLATE utf8_bin DEFAULT NULL,
  `tx_url_logotipo_cabecalho_centro` varchar(37) COLLATE utf8_bin DEFAULT NULL,
  `tx_url_logotipo_cabecalho_direita` varchar(37) COLLATE utf8_bin DEFAULT NULL,
  `tx_titulo` varchar(45) COLLATE utf8_bin NOT NULL,
  `tx_corpo` text COLLATE utf8_bin NOT NULL,
  `tx_nome_assinatura1` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_tratamento_assinatura1` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_nome_assinatura2` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_tratamento_assinatura2` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_nome_assinatura3` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_tratamento_assinatura3` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_url_logotipo_rodape_esquerda` varchar(37) COLLATE utf8_bin DEFAULT NULL,
  `tx_url_logotipo_rodape_direita` varchar(37) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_certificado`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela responsável por armazenar os modelos dos certificados que serão gerados para os alunos.';

--
-- Dumping data for table `certificado`
--

/*!40000 ALTER TABLE `certificado` DISABLE KEYS */;
INSERT INTO `certificado` (`id_certificado`,`tx_nome_modelo`,`tx_url_logotipo_cabecalho_esquerda`,`tx_url_logotipo_cabecalho_centro`,`tx_url_logotipo_cabecalho_direita`,`tx_titulo`,`tx_corpo`,`tx_nome_assinatura1`,`tx_tratamento_assinatura1`,`tx_nome_assinatura2`,`tx_tratamento_assinatura2`,`tx_nome_assinatura3`,`tx_tratamento_assinatura3`,`tx_url_logotipo_rodape_esquerda`,`tx_url_logotipo_rodape_direita`) VALUES 
 (6,0x54657374C3A36F,0x37424541353743443542433339343531463534343145333737453942373533352E4A5047,'','',0x54657374C3A36F,0x3C703E74657374653C2F703E,'','','','','','','',''),
 (8,0x4D6F64656C6F20646520546573746573,0x38393542453944374535424545353538444136343738354545344345323333412E4A5047,'','',0x436572746966696361646F20646520436F6E636C7573C3A36F20646520437572736F,0x3C70207374796C653D22746578742D616C69676E3A206A7573746966793B223E4465636C6172616D6F732070617261206F732064657669646F732066696E732C20717565203C7374726F6E673E5B256E6F6D655F616C756E6F255D3C2F7374726F6E673E2C20706F727461646F7220646F20435046206465204E266F72646D3B205B256370665F616C756E6F255D2C207265616C697A6F75206F20747265696E616D656E746F20223C7374726F6E673E5B256E6F6D655F747265696E616D656E746F255D3C2F7374726F6E673E2220656D206E6F737361206F7267616E697A612663636564696C3B266174696C64653B6F2C20636F6D20647572612663636564696C3B266174696C64653B6F206465205B25696E73657269725F63617267615F686F7261726961255D20686F72612873292E3C2F703E0D0A3C70207374796C653D22746578742D616C69676E3A206A7573746966793B223E4F20747265696E616D656E746F20746576652073657520696E266961637574653B63696F206E612064617461205B2564745F696E6963696F5F747265696E616D656E746F255D2C20636F6D2074266561637574653B726D696E6F206E612064617461205B2564745F7465726D696E6F5F747265696E616D656E746F255D2C2073656E646F206D696E6973747261646F2070656C6F20696E73747275746F72205B256E6F6D655F696E73747275746F72255D2E3C2F703E0D0A3C703E266E6273703B3C2F703E,0x43C3A9736172204175677573746F205669656972612047696F76616E69,0x5465636EC3B36C6F676F,0x4D617269612064612053696C7661,0x446F75746F7261,'','','','');
/*!40000 ALTER TABLE `certificado` ENABLE KEYS */;


--
-- Definition of table `certificado_emitido`
--

DROP TABLE IF EXISTS `certificado_emitido`;
CREATE TABLE `certificado_emitido` (
  `id_certificado_emitido` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_certificado` bigint(20) NOT NULL,
  `id_matricula` bigint(20) NOT NULL,
  `dt_emissao_certificado` date NOT NULL,
  `nr_registro_certificado` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_certificado_emitido`),
  KEY `fk_certificado_emitido_certificado` (`id_certificado`),
  KEY `fk_certificado_emitido_matricula` (`id_matricula`),
  CONSTRAINT `fk_certificado_emitido_certificado` FOREIGN KEY (`id_certificado`) REFERENCES `certificado` (`id_certificado`),
  CONSTRAINT `fk_certificado_emitido_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela responsável por armazenar os certificados gerados gerados para os alunos.';

--
-- Dumping data for table `certificado_emitido`
--

/*!40000 ALTER TABLE `certificado_emitido` DISABLE KEYS */;
INSERT INTO `certificado_emitido` (`id_certificado_emitido`,`id_certificado`,`id_matricula`,`dt_emissao_certificado`,`nr_registro_certificado`) VALUES 
 (6,8,5,'2013-09-16',0x6534646133623766626263653233343564373737326230363734613331386435),
 (7,8,6,'2013-09-16',0x3136373930393163356138383066616636666235653630383765623162326463),
 (8,8,7,'2013-09-16',0x3866313465343566636565613136376135613336646564643462656132353433),
 (9,8,8,'2013-09-16',0x6339663066383935666239386162393135396635316664303239376532333664);
/*!40000 ALTER TABLE `certificado_emitido` ENABLE KEYS */;


--
-- Definition of table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `id_cliente` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) unsigned NOT NULL,
  `tx_telefone_fixo` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `tx_telefone_celular` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `tx_nome_fantasia` varchar(60) COLLATE utf8_bin DEFAULT NULL,
  `tx_razao_social` varchar(60) COLLATE utf8_bin DEFAULT NULL,
  `tx_inscricao_estadual` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `tx_inscricao_municipal` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `tx_cnpj` varchar(18) COLLATE utf8_bin DEFAULT NULL,
  `tx_nome` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_rg` varchar(9) COLLATE utf8_bin DEFAULT NULL,
  `tx_profissao` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_cpf` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `tx_email` varchar(65) COLLATE utf8_bin DEFAULT NULL,
  `tx_nome_contato` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_tipo_cliente` char(1) COLLATE utf8_bin NOT NULL,
  `tx_cliente_aluno` char(1) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  KEY `FK_cliente_usuario` (`id_usuario`),
  CONSTRAINT `FK_cliente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela responsável por armazenar os clientes que contratam algum treinamento da consultoria.';

--
-- Dumping data for table `cliente`
--

/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` (`id_cliente`,`id_usuario`,`tx_telefone_fixo`,`tx_telefone_celular`,`tx_nome_fantasia`,`tx_razao_social`,`tx_inscricao_estadual`,`tx_inscricao_municipal`,`tx_cnpj`,`tx_nome`,`tx_rg`,`tx_profissao`,`tx_cpf`,`tx_email`,`tx_nome_contato`,`tx_tipo_cliente`,`tx_cliente_aluno`) VALUES 
 (12,18,0x2839392920393939392D39393939,0x2831312920313131312D31313131,0x4E6F6D652046616E74617369612054657374C3A36F20456469C3A7C3A36F,0x54657374C3A36F2052617AC3A36F20536F6369616C20456469C3A7C3A36F,0x31313131313131313131313131,0x323232323232323232323232323232,0x38302E3233312E3337332F303030312D3032,NULL,NULL,NULL,NULL,0x6140612E636F6D,0x43C3A973617220436F6E7461746F,0x4A,NULL),
 (14,20,0x2833322920313332332D31323332,0x2832332920313233312D32333233,NULL,NULL,NULL,NULL,NULL,0x52616E69656C61205269636172646F2043617276616C686F20456469C3A7C3A36F,0x323331323332313332,0x50726F6772616D61646F7261,0x3135362E3831332E3336332D3434,0x72616E6972726340676D61696C2E636F6D,NULL,0x46,0x53),
 (15,22,0x2831342920333332342D39353631,0x2831342920393838382D33373734,0x456469C3A7C3A36F20646520506573736F61,0x52617AC3A36F2045646974616C,0x3232323232323232323232323232,0x343434343434343434343434343434,0x32382E3533322E3831312F303030312D3532,NULL,NULL,NULL,NULL,0x63657361722E6175677573746F2E67696F76616E6940676D61696C2E636F6D,0x43C3A973C3A36F20456469C3A7C3A36F69,0x4A,NULL),
 (16,25,0x2833332920333333332D33333333,0x2833332920333333332D33333333,0x54657374C3A36F20646F204E6F6D652046616E7461736961,0x52617AC3A36F20536F6369616C205465737465,0x3333333333333333333333333333,0x353535353535353535353535353535,0x37382E3831342E3837382F303030312D3538,NULL,NULL,NULL,NULL,0x6140612E636F6D,0x436F6E7461746F205465737465,0x4A,NULL),
 (19,30,0x2832312920323132312D3231,0x2831342920393838382D33373734,NULL,NULL,NULL,NULL,NULL,0x4365736172,0x343834383039333734,0x50726F6772616D61646F72,0x3339342E3138302E3838382D3531,0x6140612E636F6D,NULL,0x46,0x4E);
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;


--
-- Definition of table `endereco`
--

DROP TABLE IF EXISTS `endereco`;
CREATE TABLE `endereco` (
  `id_endereco` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cliente` bigint(20) NOT NULL,
  `tx_logradouro` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_numero` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  `tx_bairro` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `tx_cep` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `tx_cidade` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `id_estado` bigint(20) unsigned DEFAULT NULL,
  `tx_tipo_endereco` char(1) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_endereco`),
  KEY `fk_endereco_cliente` (`id_cliente`),
  KEY `FK_endereco_estado` (`id_estado`),
  CONSTRAINT `fk_endereco_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_endereco_estado` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela responsável por armazenar os endereços dos clientes. \nCada cliente terá sempre DOIS endereços, o endereço real e o endereço para correspondência';

--
-- Dumping data for table `endereco`
--

/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
INSERT INTO `endereco` (`id_endereco`,`id_cliente`,`tx_logradouro`,`tx_numero`,`tx_bairro`,`tx_cep`,`tx_cidade`,`id_estado`,`tx_tipo_endereco`) VALUES 
 (5,12,0x5275612054657374C3A36F20636F6D204163656E746F,0x3132333333,0x43656E74726F,0x31312E3131312D313131,0x4F7572696E686F73,7,0x52),
 (6,12,0x4C6F677261646F7520456469C3A7C3A36F,'','','','',NULL,0x43),
 (9,14,0x5275612054657374C3A36F20646F73204163656E74C3B373,0x3132333233,0x4A617264696D2053616E74612046C3A9,0x31392E3930302D303030,0x4F7572696E686F73,7,0x52),
 (10,14,'','','','','',NULL,0x43),
 (11,15,0x527561204175677573746F20436172646F736F206465204D6F757261,0x313235,0x4F75726F205665726465,0x31392E3930362D323430,0x4F7572696E686F73,25,0x52),
 (12,15,'','','','','',NULL,0x43),
 (13,16,'','','','','',NULL,0x52),
 (14,16,'','','','','',NULL,0x43),
 (19,19,'','','','','',NULL,0x52),
 (20,19,'','','','','',NULL,0x43);
/*!40000 ALTER TABLE `endereco` ENABLE KEYS */;


--
-- Definition of table `estado`
--

DROP TABLE IF EXISTS `estado`;
CREATE TABLE `estado` (
  `id_estado` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tx_sigla` varchar(2) COLLATE utf8_bin NOT NULL,
  `tx_descricao` varchar(25) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela de suporte responsável por armazenar os estados do Brasil.';

--
-- Dumping data for table `estado`
--

/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` (`id_estado`,`tx_sigla`,`tx_descricao`) VALUES 
 (1,0x4143,0x41637265),
 (2,0x414C,0x416C61676F6173),
 (3,0x4150,0x416D6170C3A1),
 (4,0x414D,0x416D617A6F6E6173),
 (5,0x4241,0x4261686961),
 (6,0x4345,0x43656172C3A1),
 (7,0x4446,0x446973747269746F204665646572616C),
 (8,0x4553,0x457370C3AD7269746F2053616E746F),
 (9,0x474F,0x476F69C3A173),
 (10,0x4D41,0x4D6172616E68C3A36F),
 (11,0x4D54,0x4D61746F2047726F73736F),
 (12,0x4D53,0x4D61746F2047726F73736F20646F2053756C),
 (13,0x4D47,0x4D696E617320476572616973),
 (14,0x5041,0x506172C3A1),
 (15,0x5042,0x50617261C3AD6261),
 (16,0x5052,0x506172616EC3A1),
 (17,0x5045,0x5065726E616D6275636F),
 (18,0x5049,0x50696175C3AD),
 (19,0x524A,0x52696F206465204A616E6569726F),
 (20,0x524E,0x52696F204772616E646520646F204E6F727465),
 (21,0x5253,0x52696F204772616E646520646F2053756C),
 (22,0x524F,0x526F6E64C3B46E6961),
 (23,0x5252,0x526F7261696D61),
 (24,0x5343,0x53616E7461204361746172696E61),
 (25,0x5350,0x53C3A36F205061756C6F),
 (26,0x5345,0x53657267697065),
 (27,0x544F,0x546F63616E74696E73);
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;


--
-- Definition of table `matricula`
--

DROP TABLE IF EXISTS `matricula`;
CREATE TABLE `matricula` (
  `id_matricula` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_turma` bigint(20) NOT NULL,
  `id_aluno` bigint(20) NOT NULL,
  PRIMARY KEY (`id_matricula`),
  KEY `fk_matricula_turma` (`id_turma`),
  KEY `fk_matricula_aluno` (`id_aluno`),
  CONSTRAINT `fk_matricula_aluno` FOREIGN KEY (`id_aluno`) REFERENCES `aluno` (`id_aluno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_matricula_turma` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela responsável por armazenar as matriculas da turma.';

--
-- Dumping data for table `matricula`
--

/*!40000 ALTER TABLE `matricula` DISABLE KEYS */;
INSERT INTO `matricula` (`id_matricula`,`id_turma`,`id_aluno`) VALUES 
 (5,1,4),
 (6,1,2),
 (7,1,3),
 (8,1,1);
/*!40000 ALTER TABLE `matricula` ENABLE KEYS */;


--
-- Definition of table `treinamento`
--

DROP TABLE IF EXISTS `treinamento`;
CREATE TABLE `treinamento` (
  `id_treinamento` bigint(20) NOT NULL AUTO_INCREMENT,
  `tx_nome_treinamento` varchar(45) NOT NULL,
  `tx_descricao` text,
  `tx_conteudo_programatico` text,
  `nr_carga_horaria` float DEFAULT NULL,
  `tx_nome_instrutor` varchar(45) NOT NULL,
  `tx_funcao_instrutor` varchar(45) NOT NULL,
  `tx_nome_responsavel_tecnico` varchar(45) DEFAULT NULL,
  `tx_registro_responsavel_tecnico` varchar(20) DEFAULT NULL,
  `tx_funcao_responsavel_tecnico` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_treinamento`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabela responsável por armazenar os treinamentos ministrados pela consultoria.';

--
-- Dumping data for table `treinamento`
--

/*!40000 ALTER TABLE `treinamento` DISABLE KEYS */;
INSERT INTO `treinamento` (`id_treinamento`,`tx_nome_treinamento`,`tx_descricao`,`tx_conteudo_programatico`,`nr_carga_horaria`,`tx_nome_instrutor`,`tx_funcao_instrutor`,`tx_nome_responsavel_tecnico`,`tx_registro_responsavel_tecnico`,`tx_funcao_responsavel_tecnico`) VALUES 
 (1,'Testão do Treinamento','Teste','Teste',20,'Instrutor','Teste','Técnico','21321321','2132132');
/*!40000 ALTER TABLE `treinamento` ENABLE KEYS */;


--
-- Definition of table `turma`
--

DROP TABLE IF EXISTS `turma`;
CREATE TABLE `turma` (
  `id_turma` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_treinamento` bigint(20) NOT NULL,
  `dt_inicio_treinamento` date NOT NULL,
  `dt_termino_treinamento` date NOT NULL,
  PRIMARY KEY (`id_turma`),
  KEY `fk_turma_treinamento` (`id_treinamento`),
  CONSTRAINT `fk_turma_treinamento` FOREIGN KEY (`id_treinamento`) REFERENCES `treinamento` (`id_treinamento`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabela responsável por armazenar as turmas que participaram dos treinamentos pela consultoria.';

--
-- Dumping data for table `turma`
--

/*!40000 ALTER TABLE `turma` DISABLE KEYS */;
INSERT INTO `turma` (`id_turma`,`id_treinamento`,`dt_inicio_treinamento`,`dt_termino_treinamento`) VALUES 
 (1,1,'2012-10-09','2012-10-13');
/*!40000 ALTER TABLE `turma` ENABLE KEYS */;


--
-- Definition of table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tx_nome` varchar(45) CHARACTER SET latin1 NOT NULL,
  `tx_login` varchar(45) CHARACTER SET latin1 NOT NULL,
  `tx_senha` varchar(45) CHARACTER SET latin1 NOT NULL,
  `tx_email` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `tx_tipo_usuario` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_usuario`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `usuario`
--

/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` (`id_usuario`,`tx_nome`,`tx_login`,`tx_senha`,`tx_email`,`tx_tipo_usuario`) VALUES 
 (1,'nome do cliente','2','2','1',2),
 (2,'nome do admin','1','1','1',1),
 (3,'nome do aluno','3','3','1',3),
 (18,'Testão Razão Social','CLIENTE_80231373000102','12345','a@a.com',2),
 (20,'Raniela Ricardo Carvalho','CLIENTE_15681336344','12345','ranirrc@gmail.com',2),
 (21,'Raniela Ricardo Carvalho','ALUNO_15681336344','12345','ranirrc@gmail.com',3),
 (22,'César Augusto Vieira Giovani','CLIENTE_39418088851','12345','cesar.augusto.giovani@gmail.com',2),
 (23,'Testetsetes teste','ALUNO_25976227112','12345',NULL,3),
 (24,'Pedro Meneguel','ALUNO_46734711560','12345',NULL,3),
 (25,'Razão Social Teste','CLIENTE_78814878000158','12345','a@a.com',2),
 (26,'João da Silva Sauro','a','12345','cesar.augusto.giovani@gmail.com',3),
 (28,'Cesar','2121','212121','a@a.com',1),
 (30,'Cesar','CLIENTE_39418088851','12345','a@a.com',2),
 (31,'abc','abc','abc','a@ac.om',1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
