USE dbcrawlermerlot;

CREATE TABLE IF NOT EXISTS `app_errors` (
  `idcategory` int(11) NOT NULL,
  `MAE`        double  NOT NULL,
  `RMS`        double  NOT NULL,
  PRIMARY KEY (`idcategory`)
);
