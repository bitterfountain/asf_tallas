<?php

class Combinations extends ObjectModel
{
  
    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function getProductAttributeCombinations($id_product) 
    {
        return $this->get_select_attr_product($id_product);
    }

    public function get_select_attr_product($id_product)
    {
        if ( $this->is_cached($id_product)!=false ) 
           return $this->cached_result;

        $colores = Array();

        $sql = '
                SELECT DISTINCT
                      pac.id_attribute
                    , al.name as color_name
                FROM
                    ps_product_attribute AS pa
                    INNER JOIN ps_product_attribute_combination AS pac 
                        ON (pa.id_product_attribute = pac.id_product_attribute)
                    INNER JOIN ps_attribute AS a
                        ON (pac.id_attribute = a.id_attribute)
                    INNER JOIN ps_attribute_group AS ag
                        ON (a.id_attribute_group = ag.id_attribute_group)
                    INNER JOIN ps_attribute_lang AS al 
                        ON (al.id_attribute = a.id_attribute)
                WHERE (pa.id_product ='.$id_product.'
                    AND ag.is_color_group =0
                    AND a.id_attribute_group =1
                    AND al.id_lang ='.$this->context->language->id.')
                ORDER BY pac.id_attribute

                ';

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        foreach ( $res as $item ) 
        {   
            $sql = '
                SELECT
                      \''.$item['id_attribute'].'\' AS id_attribute_color 
                    , \''.$item['color_name'].'\' AS color_name
                    , a.id_attribute AS id_attribute_talla     
                    , pa.id_product_attribute     
                    , agl.public_name
                    , al.name
                    , sa.quantity
                FROM
                    ps_product_attribute AS pa
                    INNER JOIN ps_product_attribute_combination AS pac 
                        ON (pa.id_product_attribute = pac.id_product_attribute)
                    INNER JOIN ps_stock_available as sa
                        ON (pa.id_product = sa.id_product) AND (pa.id_product_attribute = sa.id_product_attribute)
                    INNER JOIN ps_attribute AS a 
                        ON (a.id_attribute = pac.id_attribute)
                    INNER JOIN ps_attribute_group_lang AS agl
                        ON (a.id_attribute_group = agl.id_attribute_group)
                    INNER JOIN ps_attribute_lang AS al
                        ON (a.id_attribute = al.id_attribute)
                    INNER JOIN ps_attribute_group AS ag 
                        ON (ag.id_attribute_group = agl.id_attribute_group)

                WHERE (pa.id_product ='.$id_product.'
                    AND agl.id_lang ='.$this->context->language->id.'
                    AND al.id_lang ='.$this->context->language->id.'                    
                    AND pac.id_product_attribute IN (
                        SELECT
                            pac.id_product_attribute
                        FROM
                            ps_product_attribute AS pa
                            INNER JOIN ps_product_attribute_combination AS pac
                            ON (pa.id_product_attribute = pac.id_product_attribute)
                        WHERE (pa.id_product ='.$id_product.'
                            AND pac.id_attribute ='.$item['id_attribute'].')                    
                    )
                    AND ag.is_color_group =0
                    AND a.id_attribute_group = 1
                    AND ag.id_attribute_group = 1

                    )   
                           
                ORDER BY id_attribute_color, id_product_attribute                    
            ';

            $tallas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            
            $arr_sel = Array();

            $total = 0;

            foreach ( $tallas as $talla ) 
            {
                $total = $total + $talla['quantity'];
                
                if ( $talla['quantity'] > 0 )   {
                    $class_talla                = "";
                    if ( $talla['quantity'] > 1 )   {
                        $title_out_stock['es']      = sprintf( 'Quedan %d unidades.', $talla['quantity']) ;
                        $title_out_stock['en']      = sprintf( 'Available units: %d', $talla['quantity']) ;
                    } else {
                        $title_out_stock['es']      = 'Atención! Última unidad en stock!!';
                        $title_out_stock['en']      = 'Warning! Just one unit available!!';                 
                    }
                } else {
                    $class_talla="sin_stock";
                    $title_out_stock['es']      = 'Esta talla esta fuera de stock';
                    $title_out_stock['en']      = 'This combination is out of stock';
                }
                
                /*
                //$class_talla              = "";
                $title_out_stock['es']      = 'Selecciona y mete a tu cesta.';
                $title_out_stock['en']      = 'Select and put into your cart.';
                */

                array_push($arr_sel, Array(
                    "id_attribute_color"    => $talla['id_attribute_color'],
                    "id_attribute_talla"    => $talla['id_attribute_talla'],
                    "color_name"            => $talla['color_name'],
                    "attribute_name"        => $talla['name'],
                    "quantity"              => $talla['quantity'],
                    "id_product_attribute"  => $talla['id_product_attribute'],
                    "class_talla"           => $class_talla,
                    "title_out_stock"       => $title_out_stock[$this->context->language->iso_code],
                    "public_name"           => $talla['public_name']
                ) );
            }
            
            array_push($colores, Array(
                "color_name"        =>  $item['color_name'],
                "id_attribute"      =>  $item['id_attribute'],
                "id_product"        =>  $id_product,
                "tallas"            =>  $arr_sel,
                "total"             =>  $total
            ));
        }

        $this->cachea_result($id_product, $colores );

        return $colores;
    }

    function cachea_result($id_product, $array_tallas)
    {
        $sql = ' SELECT id_product FROM asf_cache_tallas WHERE id_product = ' . $id_product;

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS( $sql );
      

        if ($res!=false && count($res)>0 && $res[0]['id_product'] == $id_product) {

            //public function update($table, $data, $where = '', $limit = 0, $null_values = false, $use_cache = true, $add_prefix = true)
            $res = Db::getInstance()->update('asf_cache_tallas', array(
                'arr_tallas'    => serialize($array_tallas),
                'fecha'         => date('Y-m-d H:i:s'),
                'is_updated'    => 1,
            ), ' id_product = ' . $id_product, 0, false, true, false);

            //$sql = " UPDATE asf_cache_tallas SET is_updated = 1, id_product = $id_product, arr_tallas = '".$array_tallas."', fecha = '".date('Y-m-d H:i:s')."'  ";
            //$res = Db::getInstance()->ExecuteS($sql);        
        } else {

            $res = Db::getInstance()->insert('asf_cache_tallas', array(
                'id_product'    => $id_product,
                'arr_tallas'    => serialize($array_tallas),
                'fecha'         => date('Y-m-d H:i:s'),
                'is_updated'    => 1,
            ), false, true, Db::INSERT, false);

            //$sql = " INSERT INTO asf_cache_tallas (id_product, arr_tallas, fecha, is_updated) VALUES ($id_product, '".$array_tallas."', '".date('Y-m-d H:i:s')."', 1) ";
            //$res = Db::getInstance()->ExecuteS($sql);     
        }
    }

    function is_cached($id_product)
    {

        $this->cached_result = '';

        $sql = ' SELECT * FROM asf_cache_tallas WHERE id_product = ' . (int)$id_product . ' AND is_updated = 1 ';

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($res!=false && $res[0]['id_product']==$id_product ) 
        {
            $this->cached_result = unserialize($res[0]['arr_tallas']);
            return true;
        } else {
            return false;
        }
    } 
          



}
