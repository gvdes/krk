<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonImmutable;

class SsolController extends Controller
{
    private $conn = null;
    private $laps = [ "ini"=>null, "end"=>null ];

    public function __construct(){
        $access = env("ACCDB_FILE");

        if(file_exists($access)){
            try{
                $this->conn = new \PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)};charset=UTF-8; DBQ=".$access."; Uid=; Pwd=;");
            }catch(\PDOException $e){ die($e->getMessage()); }
        }else{ die("$access no es un origen de datos valido."); }
    }

    public function productsUpdated($laps){
        $ini = $laps["ini"];
        $end = $laps["end"];

        try{
            $qprods = "SELECT * FROM F_ART WHERE FUMART Between #".$ini."# AND #".$end."#";
            $exec = $this->conn->prepare($qprods);
            $exec->execute();
            $products = $exec->fetchAll(\PDO::FETCH_ASSOC);

            $qprices = "SELECT
                PL.PRELTA,
                PL.TARLTA ,
                PL.ARTLTA
                FROM F_LTA AS PL
                INNER JOIN F_ART AS ART ON PL.ARTLTA=ART.CODART
                WHERE ART.FUMART Between #".$ini."# AND #".$end."#";
            $exec = $this->conn->prepare($qprices);
            $exec->execute();
            $prices = $exec->fetchAll(\PDO::FETCH_ASSOC);

            return ["products" => $products, "prices"=>$prices];

        }catch (\PDOException $e){ die($e->getMessage()); }
    }
}

/**
 *             $q = "SELECT
                    A.CODART,
                    A.CCOART,
                    A.EANART,
                    A.DESART,
                    A.DEEART,
                    A.REFART,
                    A.UPPART,
                    A.FAMART,
                    A.CP1ART,
                    A.PCOART,
                    A.NPUART,
                    A.PHAART,
                    A.DIMART,
                    A.CP5ART,
                    A.FUMART,
                    A.FALART,
                    PMEN.PRELTA AS PMEN,
                    PMAY.PRELTA AS PMAY,
                    PDOC.PRELTA AS PDOC,
                    PCAJ.PRELTA AS PCAJ
                FROM ((((
                        F_ART AS A
                        LEFT JOIN F_LTA AS PMEN ON (PMEN.ARTLTA=A.CODART AND PMEN.TARLTA=1)
                    )LEFT JOIN F_LTA AS PMAY ON (PMAY.ARTLTA=A.CODART AND PMAY.TARLTA=2)
                    )LEFT JOIN F_LTA AS PDOC ON (PDOC.ARTLTA=A.CODART AND PDOC.TARLTA=3)
                    )LEFT JOIN F_LTA AS PCAJ ON (PCAJ.ARTLTA=A.CODART AND PCAJ.TARLTA=4)
                    )
                WHERE A.FUMART Between #".$ini."# AND #".$end."#";
 */
