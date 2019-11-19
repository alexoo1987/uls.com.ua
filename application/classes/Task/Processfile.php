<?php defined('SYSPATH') or die('No direct script access.');
class Task_Processfile extends Minion_Task {
    protected $_options = array(
        'sess_data' => "",
    );

    protected function _execute(array $params)
    {
	try {
            Minion_CLI::write('Start process file');
	    Log::$write_on_add = true;
	    Kohana::$log->add(Log::INFO, 'Start process file');
       	    $this->process_file();
        } catch (Exception $e) {
            Kohana::$log->add(Log::INFO, $e->getFile().":".$e->getLine()."\n".$e->getMessage());
            return FALSE;
        }

    }
 
    public function process_file()
    {
    	$sess_data = NULL;
    	$supplier = NULL;

        $options = $this->get_options();
    	try {
        	$sess_data = json_decode(base64_decode(str_replace('_','=',$options['sess_data'])), true);
    
        	$supplier = ORM::factory('Supplier')->where('id', '=', $sess_data['supplier_id'])->find();
        	$supplier->status = "process";
        	$supplier->error = "";
        	$supplier->total_processed = 0;
        	$supplier->total_upload = $sess_data['lines_count'];
        
        	$supplier->save();
    	} catch (Exception $e) {
    		Minion_CLI::write($e->getFile().":".$e->getLine()."\n".$e->getMessage());
		Kohana::$log->add(Log::INFO, $e->getFile().":".$e->getLine()."\n".$e->getMessage());
    		return FALSE;
    	}
	$returned_data = array();
        //do {
            try {
                $tmp = $this->action_proccess($sess_data);
                
                $sess_data = $tmp[0];
                $returned_data = $tmp[1];
                
                //$supplier->total_processed = $sess_data['lines_processed'];
                //$supplier->save();
            } catch (Exception $e) {
                $supplier->status = "error";
                // $supplier->error = $e->getMessage();
                $supplier->error = $e->getFile().":".$e->getLine()."\n".$e->getMessage();
                $supplier->save();
                Minion_CLI::write('Eroor'.$e->getFile().":".$e->getLine()."\n".$e->getMessage());
                //break;
            }
        //} while(isset($returned_data['status']) && $returned_data['status'] == 'continue');
    }
    
    private $part_factory = NULL;
    private function action_proccess($sess_data) {

        Minion_CLI::write('Start process supplier '.$sess_data['supplier_id']);
	Kohana::$log->add(Log::INFO, 'Start process supplier '.$sess_data['supplier_id']);
        // gc_enable();

        $counter = 0;
        $lines_count = $sess_data['lines_count'];
        
        $f = fopen('php://memory', 'w+');
//        $file_content = file_get_contents($sess_data['filepath']);

        try {
            fwrite($f, iconv('CP1251', 'UTF-8', file_get_contents($sess_data['filepath'])));
        } catch (Exception $e) {
            try {
                fwrite($f, iconv('CP1251', 'UTF-8//IGNORE', file_get_contents($sess_data['filepath'])));
            } catch (Exception $e) {
                fwrite($f, mb_convert_encoding(file_get_contents($sess_data['filepath']), 'UTF-8'));
            }
        }

        rewind($f);
        
        $supplier = ORM::factory('Supplier')->where('id', '=', $sess_data['supplier_id'])->find();

        $currency_id = $supplier->currency->id;

        $trim_charset = " \t\n\r\0.'\"(),";

        $price_item_query = null;
        $unmatched_query = null;
        $price_item_count = 0;
        $unmatched_count = 0;

        if($this->part_factory == NULL) {
           $this->part_factory = ORM::factory('Part');
        }

        for($i = 0; $data = fgetcsv($f, 0, ';', '"'); $i++) {
            //if($i < $sess_data['lines_processed']) continue;
            //if($counter >= 5000) break;

            if($price_item_query == null) {
                $price_item_query = DB::insert('priceitems', array('part_id', 'price',
                                                                   'currency_id', 'amount', 'delivery', 'supplier_id',
                                                                   'operation_id'));
            }

            if($unmatched_query == null) {
                $unmatched_query = DB::insert('unmatched', array('article', 'brand', 'name', 'reason', 'price',
                                                                 'currency_id', 'amount', 'delivery', 'supplier_id',
                                                                 'operation_id'));
            }
            
            //Set description
            $name = (!empty($sess_data['columns']['name'])) ? trim($data[$sess_data['columns']['name']]) : '';
            
            //Set price
            $price = preg_replace('/[^0-9.,]+/i', '', $data[$sess_data['columns']['price']]);
            $price = (float)str_replace(",", ".",$price);

            //Set amount and delivery
            $amount = null;
            foreach($sess_data['stores'] as $store) {
                $amount = (!empty($store['amount_column'])) ? trim($data[$store['amount_column']]) : '';
                $delivery = (!empty($store['delivery_column'])) ? trim($data[$store['delivery_column']]) : $store['delivery'];
                if (empty($amount) OR $amount == '0') continue;

                //Set brand
                $brand_long = trim($data[$sess_data['columns']['brand']], $trim_charset);

                //Set article
                $article_long = trim($data[$sess_data['columns']['article']], $trim_charset);
                if ($sess_data['remove_first'] == 1) $article_long = preg_replace('/^[a-zA-Z]+/i', '', $article_long);

                $part  = $this->part_factory->get_article($article_long, $brand_long, $name, $sess_data['operation_id']);
                if (!$part || empty($amount) || $amount == '0') {
                    $sess_data['lines_processed']++;
                    $counter++;
                    if ($counter >= 500) {
                        $counter = 0;
                        $supplier->total_processed = $sess_data['lines_processed'];
                        $supplier->save();
                    }
                    continue;
                }
                if ($amount == "+") $amount = 1;
                else $amount = preg_replace('/[^0-9]/', '', $amount);

                $price_column = $price * $sess_data['ratio'];
                if ($part == 'bad_brand') {
                    $unmatched_query->values(array(
                        $article_long, $brand_long, $name, $part, $price_column, $currency_id,
                        $amount, $delivery, $sess_data['supplier_id'], $sess_data['operation_id']
                    ));
                    $unmatched_count++;
                } else if ($part == 'bad_article') {

                    $brand_instance = ORM::factory('Brand')
                        ->get_brand($brand_long, $sess_data['operation_id'], 0);
                    $article_long = trim($article_long, $trim_charset);
                    $article_long = $brand_instance->apply_rules($article_long);
                    $article = Article::get_short_article($article_long);

                    $unmatched_query->values(array(
                        $article, $brand_instance->brand, $name, $part, $price_column, $currency_id,
                        $amount, $delivery, $sess_data['supplier_id'], $sess_data['operation_id']
                    ));
                    $unmatched_count++;

                } else {
                    $price_item_query->values(array(
                        $part->id, $price_column, $currency_id,
                        $amount, $delivery, $sess_data['supplier_id'], $sess_data['operation_id']
                    ));
                    $price_item_count++;
                }
            }

            if($unmatched_count > 2000) {
                $unmatched_query->execute();
                $unmatched_query = null;
                $unmatched_count = 0;
            }

            if($price_item_count > 2000) {
                $price_item_query->execute();
                $price_item_query = null;
                $price_item_count = 0;
            }

            $sess_data['lines_processed']++;
            $counter++;
            if($counter >= 500) {
                $counter = 0;
                $supplier->total_processed = $sess_data['lines_processed'];
                $supplier->save();

                Minion_CLI::write(memory_get_usage());
            }
        }


        if($unmatched_count > 0) {
            $unmatched_query->execute();
        }

        if($price_item_count > 0) {
            $price_item_query->execute();
        }
        
        
        $json['current'] = $sess_data['lines_processed'];
        $json['total'] = $lines_count;
        $json['status'] = ($sess_data['lines_processed'] >= $lines_count) ? "complete" : "continue";
        fclose($f);
        
        if($json['status'] == "complete") {
            //$supplier = ORM::factory('Supplier')->where('id', '=', $sess_data['supplier_id'])->find();
            
            $supplier->update_time = date("Y-m-d H:i:s");
            $supplier->update_count = ORM::factory('Priceitem')->where('supplier_id', '=', $sess_data['supplier_id'])->count_all();
            $supplier->status = "ready";
            
            $supplier->save();
        }

        Minion_CLI::write('End process supplier '.$sess_data['supplier_id']);
	Kohana::$log->add(Log::INFO, 'End process supplier '.$sess_data['supplier_id']);
        
        return array(0 => $sess_data, 1 => $json);
    }
}
?>
