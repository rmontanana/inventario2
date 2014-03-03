<?php
/**
 * @author Olivier Plathey
 * @author Ricardo Montañana Gómez
 * @copyright Copyright (c) 2008, Ricardo Montañana Gómez
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * This file is part of Inventario.
 * Inventario is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Inventario is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Inventario.  If not, see <http://www.gnu.org/licenses/>.
 *  
 */

class Pdf_mysql_table extends Fpdf
{
    /**
     * Modificado por Ricardo Montañana 05/2008 para añadir la posibilidad de cálculo de totales
     * @var $totales float[] Vector de totales de las columnas que lo necesiten
     */
  private $ProcessingTable=false,$aCols=array(),$TableX,$HeaderColor;
  private $RowColors,$ColorIndex;
  private $bdd,$titulo,$cabecera;
  private $totales=array(),$procesandoTotales=false;
  /**
   * 
   * @param mixed $bdd Controlador de la base de datos
   * @param string $orientacion Orientación de la página P/L
   * @param string $formato Tamaño de la página p. ej. A4
   * @param string $titulo Título del informe
   * @param string $cabecera Texto para la cabecera
   */
  

    public function __construct($bdd,$orientacion,$formato,$titulo='',$cabecera='')
    {
      $this->bdd=$bdd;
      $this->titulo=$titulo;
      $this->cabecera=$cabecera;
      parent::__construct($orientacion,'mm',$formato);
    }
    public function setTitulo($titulo)
    {
      $this->titulo=$titulo;
    }
    public function iniciaTotales()
    {
        $this->totales = array();
    }
    function Header()
    {
      //Modficada por Ricardo Montañana
        //Titulo
        $fecha=strftime("%d-%b-%Y %H:%M");
        $this->SetFont('Arial','',8);
        $this->Cell(0,4,html_entity_decode(CENTRO . " " . APLICACION,ENT_COMPAT | ENT_HTML401,'ISO-8859-1'),0,1,'L');
        $this->SetFont('Arial','',18);
        $this->Cell(0,6,utf8_decode($this->titulo),0,1,'C');
        $this->SetFont('Arial','',8);
        $this->Cell(0,3,$fecha,0,1,'R');
        $this->Cell(0,5,utf8_decode($this->cabecera),0,1,'C');
        $this->Ln(10);
        //Print the table header if necessary
        if($this->ProcessingTable)
            $this->TableHeader();
        //Ensure table header is output
        parent::Header();   
    }
    public function Footer()
    {
      $this->SetFont('Arial','',8);
      $this->setY($this->h-10);
      $this->Cell(0,6,'-'.$this->PageNo().'-',0,1,'C');
      parent::Footer();
    }

    function TableHeader()
    {
        $this->SetFont('Arial','B',12);
        $this->SetX($this->TableX);
        $fill=!empty($this->HeaderColor);
        if($fill)
            $this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
        foreach($this->aCols as $col)
            $this->Cell($col['w'],6,utf8_decode($col['c']),1,0,'C',$fill);
        $this->Ln();
    }

    function Row($data)
    {
        $this->SetX($this->TableX);
        $ci=$this->ColorIndex;
        $fill=!empty($this->RowColors[$ci]);
        if($fill)
            $this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
        foreach($this->aCols as $col) {
            switch ($col['a']) {
                case 'D':$alin='R';break;
                case 'I':$alin='L';break;
                case 'C':$alin='C';break;
                default:$alin='L';break;
            }
            if ($this->procesandoTotales) {
                $this->SetFont('Arial','B',12);
            }
            $this->Cell($col['w'],5,utf8_decode($data[$col['f']]),1,0,$alin,$fill);
            //$this->Cell($col['w'],5,utf8_decode($data[$col['f']]),1,0,$alin,$fill);
            //$this->Cell($col['w'],5,utf8_decode($data['proveedor']),1,0,$alin,$fill);
            //$this->Write(5,"nombre=".$col['f'].",titulo=".$col['c'].",ancho=".$col['w'].",alin=".$col['a']);
            //$this->Write(5,$data[$col['f']].$col['f']);
            //print_r($data);
            //print_r($this->aCols);
            if ($col['t']=='S' && !$this->procesandoTotales) {
                $this->totales[$col['f']]+=$data[$col['f']];
            }
            
        }
        $this->Ln();
        $this->ColorIndex=1-$ci;
    }

    function CalcWidths($width,$align)
    {
        //Compute the widths of the columns
        $TableWidth=0;
        foreach($this->aCols as $i=>$col)
        {
            $w=$col['w'];
            if($w==-1)
                $w=$width/count($this->aCols);
            elseif(substr($w,-1)=='%')
                $w=$w/100*$width;
            $this->aCols[$i]['w']=$w;
            $TableWidth+=$w;
        }
        //Compute the abscissa of the table
        if($align=='C')
            $this->TableX=max(($this->w-$TableWidth)/2,0);
        elseif($align=='R')
            $this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
        else
            $this->TableX=$this->lMargin;
    }

    function AddCol($field=-1,$width=-1,$caption='',$align='I',$total='N')
    {
        //Add a column to the table
        if($field==-1)
            $field=count($this->aCols);
        $this->aCols[]=array('f'=>$field,'c'=>$caption,'w'=>$width,'a'=>$align,'t'=>$total);
    }

    function Table($query,$prop=array())
    {
        //Issue query
        $res=$this->bdd->query($query) or die('Error: '.$this->bdd->mysql_error()."<BR>Query: $query");
        //Add all columns if none was specified
        if(count($this->aCols)==0)
        {
            $nb=$res->field_count;
            for($i=0;$i<$nb;$i++)
                $this->AddCol();
        }
        //Retrieve column names when not specified
        $i=0;
        foreach($this->aCols as $i=>$col)
        {
            if($col['c']=='')
            {
                if(is_string($col['f']))
                    $this->aCols[$i]['c']=ucfirst($col['f']);
                else
                    $this->aCols[$i]['c']=ucfirst($res->field_seek($i));
            }
            $i++;
        }
        //Handle properties
        if(!isset($prop['width']))
            $prop['width']=0;
        if($prop['width']==0)
            $prop['width']=$this->w-$this->lMargin-$this->rMargin;
        if(!isset($prop['align']))
            $prop['align']='C';
        if(!isset($prop['padding']))
            $prop['padding']=$this->cMargin;
        $cMargin=$this->cMargin;
        $this->cMargin=$prop['padding'];
        if(!isset($prop['HeaderColor']))
            $prop['HeaderColor']=array();
        $this->HeaderColor=$prop['HeaderColor'];
        if(!isset($prop['color1']))
            $prop['color1']=array();
        if(!isset($prop['color2']))
            $prop['color2']=array();
        $this->RowColors=array($prop['color1'],$prop['color2']);
        //Compute column widths
        $this->CalcWidths($prop['width'],$prop['align']);
        //Print header
        $this->TableHeader();
        //Print rows
        $this->SetFont('Arial','',11);
        $this->ColorIndex=0;
        $this->ProcessingTable=true;
        $this->procesandoTotales=false;
        while($row=$res->fetch_assoc()) {
            $this->Row($row);
        }
        $this->procesandoTotales=true;
        // Procesa los totales
        if ($this->procesaTotales()) {
            $this->Row($this->totales);
        }
        $this->ProcessingTable=false;
        $this->cMargin=$cMargin;
        $this->aCols=array();
    }
    /**
     * Se encarga de generar una línea de totalización si es necesario
     * @param array $datos Línea con los totales a imprimir o NULL
     * @return boolean Si hay que generar la línea o no
     */
    private function procesaTotales()
    {
        foreach($this->aCols as $col) {
            if ($col['t']=='S') {
                return true;
            }
        }
        return false;
    }
}
?>