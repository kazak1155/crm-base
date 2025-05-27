<?php
namespace Plugins;

class DB_get_session
{
	private $Factory;

	function __construct($Factory)
	{
		$this->Factory = $Factory;
		/*
 		* TODO - this shit is broken
		*/
		if(isset($source['rowid']))
		{
			$haul_id = $source['rowid'];
			$haul_text = 'ТИГ --- Рейс #'.$source['rowid_text'];
			$printed = $this->catchPDO("SELECT Код,Клиенты_Код,Фабрики_Код FROM Форма_заказы WHERE Рейсы_Код = $haul_id AND Удалено = 0 ORDER BY Клиенты_Код ASC");
			if($fp = fsockopen($this->server_prm['sql02_ip'],1433,$errno, $errstr, 10)){
				fclose($fp);
				try
				{
					$wh_connection = new PDO("sqlsrv:server=".$this->server_prm['sql02_ip']."; Database=".$this->server_prm['sql02_wh_db_name'], "");
					$wh_connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					$wh_connection->beginTransaction();
				}
				catch(Exception $e )
				{
					die('Склад не доступен.'.$e->getMessage());
				}
				$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_table_name']." (Поставщик_Код,Описание) VALUES (".$this->server_prm['tig_id'].",".$this->PDO_quote($haul_text).")",$wh_connection,true);
				$income_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_table_name']."') AS int",$wh_connection)->fetchColumn();
				$orders = $this->catchPDO("SELECT Код,Клиенты_Код,Фабрики_Код FROM Форма_заказы WHERE Рейсы_Код = $haul_id AND Удалено = 0 ORDER BY Клиенты_Код ASC");
				$pver_owner_id = 0;
				while($order = $orders->fetch(PDO::FETCH_ASSOC))
				{
					$wh_client_goods = array();
					$order = $this->iconvKeys($order, 'cp1251', 'utf-8');
					$owner_id = $order['Клиенты_Код'];
					$manufacturer_id = $order['Фабрики_Код'];
					$order_barcodes = $this->catchPDO("SELECT ПолныйШтрихкод FROM V_Штрихкоды WHERE Заказы_Код = ".$order['Код'])->fetchAll(PDO::FETCH_COLUMN,0);
					$order_details = $this->catchPDO("SELECT Виды_груза_Код,Виды_груза.Название as Вид_груза,Артикул,Кол_предм,Кол_мест,Объем,Вес,Примечание FROM Заказы_Состав
						LEFT JOIN Виды_груза ON Виды_груза.Код = Заказы_Состав.Виды_груза_Код
						WHERE Заказы_Код = ".$order['Код']);
					if($owner_id !== $pver_owner_id)
						$order_volume_sum = 0;
					$goods_volume_sum = 0;
					$order_barcode_amount = 0;
					while($details = $order_details->fetch(PDO::FETCH_ASSOC))
					{
						$details = $this->iconvKeys($details, 'cp1251', 'utf-8');
						$order_volume_sum += floatval($details['Объем']);
						$goods_volume_sum += floatval($details['Объем']);
						$order_barcode_amount += intval($details['Кол_мест']);
						if(empty($details['Вид_груза']))
							continue;
						$wh_goods_type_id = $this->catchPDO("SELECT Код FROM Грузы_Типы WHERE Название = ".$this->PDO_quote($details['Вид_груза']),$wh_connection)->fetchColumn();
						if(empty($wh_goods_type_id))
						{
							$this->catchPDO("INSERT INTO Грузы_Типы (Название) VALUES (".$this->PDO_quote($details['Вид_груза']).")",$wh_connection,true);
							$wh_goods_type_id = $this->catchPDO("SELECT IDENT_CURRENT('Грузы_Типы') AS int",$wh_connection)->fetchColumn();
						}
						$wh_client_goods_data = ['Кол_ШК'=>intval($details['Кол_мест']),'Вид_груза'=>$details['Вид_груза'],'Приходы_Клиенты_Код'=>0,
							'Тип_Груза_Код'=>$wh_goods_type_id,'Производитель_Код'=>$manufacturer_id,'Артикул'=>empty($details['Артикул']) ? 'NULL': $details['Артикул'],
							'Количество'=>empty($details['Кол_предм']) ? 'NULL': $details['Кол_предм'],'Объем'=>empty($details['Объем']) ? 'NULL': $details['Объем'],
							'Вес'=>empty($details['Вес']) ? 'NULL': $details['Вес'],	'Примечание'=>empty($details['Примечание']) ? ' ': $details['Примечание']
						];
						array_push($wh_client_goods,$wh_client_goods_data);
					}
					if($order_barcode_amount == 0)
						continue;
					if($owner_id !== $pver_owner_id)
					{
						$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_clients_table_name']." (Приходы_Код,Грузовладелец_Код,Объем) VALUES ($income_id,$owner_id,$order_volume_sum)",$wh_connection,true);
						$income_cliend_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_clients_table_name']."') AS int",$wh_connection)->fetchColumn();
					}
					else
						$this->catchPDO("UPDATE ".$this->server_prm['wh_income_clients_table_name']." SET Объем = ".$this->PDO_quote($order_volume_sum)." WHERE Код = ".$this->PDO_quote($income_cliend_id),$wh_connection,true);
					$goods_count = count($wh_client_goods);
					foreach ($wh_client_goods as $key => $value)
					{
						$value['Приходы_Клиенты_Код'] = $income_cliend_id;
						if(count($order_barcodes) > 0)
						{
							$value['Печатать'] = 0;
							if($goods_count == 1)
							{
								unset($value['Кол_ШК']);
								unset($value['Вид_груза']);
								$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_table_name']." (".implode(',',array_keys($value)).") VALUES (".implode(', ',array_map(array($this, 'PDO_quote'),array_values($value))).")",$wh_connection,true);
								$wh_goods_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_goods_table_name']."') AS int",$wh_connection)->fetchColumn();
								$order_barcodes = array_map(array($this, 'PDO_quote'),$order_barcodes);
								array_walk($order_barcodes, function(&$item) { $item = $item.')'; });
								$order_barcodes = "($wh_goods_id,".implode(",($wh_goods_id,",$order_barcodes);
								$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_barcodes_table_name']." (Грузы_Код,ШК) VALUES $order_barcodes",$wh_connection,true);
							}
							elseif($goods_count > 1)
							{
								$zeros = 0;
								$goods_type_string = '';
								foreach ($wh_client_goods as $keys => $values)
								{
									if($values['Кол_ШК'] == 0)
										$zeros++;
									if($keys != 0)
									{
										if(($keys + 1) < $goods_count)
										{
											if(strlen($values['Примечание']) > 1)
												$goods_type_string .= $keys.'. '.$values['Вид_груза'].': '.$values['Примечание'].'@CRLF';
											else
												$goods_type_string .= $keys.'. '.$values['Вид_груза'].'@CRLF';
										}
										elseif(($keys + 1) == $goods_count)
										{
											if(strlen($values['Примечание']) > 1)
												$goods_type_string .= $keys.'. '.$values['Вид_груза'].': '.$values['Примечание'];
											else
												$goods_type_string .= $keys.'. '.$values['Вид_груза'];
										}
									}
								}
								if($zeros > 0)
								{
									if($wh_client_goods[0]['Кол_ШК'] != 0)
									{
										if($zeros == ($goods_count - 1))
										{
											if($value['Примечание'] != 'NULL')
												$value['Примечание'] = $value['Примечание'].'. '.$goods_type_string;
											else
												$value['Примечание'] = $goods_type_string;
											unset($value['Кол_ШК']);
											unset($value['Вид_груза']);
											$goods_type_string = $value['Примечание'];
											unset($value['Примечание']);
											$value['Объем'] = $goods_volume_sum;
											$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_table_name']." (".implode(',',array_keys($value)).",Примечание) VALUES (".implode(', ',array_map(array($this, 'PDO_quote'),array_values($value))).",REPLACE(".$this->PDO_quote($goods_type_string).",'@CRLF',CHAR(13)))",$wh_connection,true);
											$wh_goods_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_goods_table_name']."') AS int",$wh_connection)->fetchColumn();
											$order_barcodes = array_map(array($this, 'PDO_quote'),$order_barcodes);
											array_walk($order_barcodes, function(&$item) { $item = $item.')'; });
											$order_barcodes = "($wh_goods_id,".implode(",($wh_goods_id,",$order_barcodes);
											$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_barcodes_table_name']." (Грузы_Код,ШК) VALUES $order_barcodes",$wh_connection,true);
											break;
										}
										else
										{
											$value['Тип_Груза_Код'] = $this->server_prm['unkown_goods_id'];
											if($value['Примечание'] != 'NULL')
												$value['Примечание'] = $value['Вид_груза'].':'.$value['Примечание'].'.@CRLF'.$goods_type_string.'. ';
											else
												$value['Примечание'] = $value['Вид_груза'].'.@CRLF'.$goods_type_string;
											unset($value['Кол_ШК']);
											unset($value['Вид_груза']);
											$goods_type_string = $value['Примечание'];
											unset($value['Примечание']);
											$value['Объем'] = $goods_volume_sum;
											$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_table_name']." (".implode(',',array_keys($value)).",Примечание) VALUES (".implode(', ',array_map(array($this, 'PDO_quote'),array_values($value))).",REPLACE(".$this->PDO_quote($goods_type_string).",'@CRLF',CHAR(13)))",$wh_connection,true);
											$wh_goods_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_goods_table_name']."') AS int",$wh_connection)->fetchColumn();
											$order_barcodes = array_map(array($this, 'PDO_quote'),$order_barcodes);
											array_walk($order_barcodes, function(&$item) { $item = $item.')'; });
											$order_barcodes = "($wh_goods_id,".implode(",($wh_goods_id,",$order_barcodes);
											$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_barcodes_table_name']." (Грузы_Код,ШК) VALUES $order_barcodes",$wh_connection,true);
											break;
										}
									}
									else
									{
										$value['Тип_Груза_Код'] = $this->server_prm['unkown_goods_id'];
										if($value['Примечание'] != 'NULL')
											$value['Примечание'] = $value['Вид_груза'].':'.$value['Примечание'].'.@CRLF'.$goods_type_string.'. ';
										else
											$value['Примечание'] = $value['Вид_груза'].'.@CRLF'.$goods_type_string;
										unset($value['Кол_ШК']);
										unset($value['Вид_груза']);
										$goods_type_string = $value['Примечание'];
										unset($value['Примечание']);
										$value['Объем'] = $goods_volume_sum;
										$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_table_name']." (".implode(',',array_keys($value)).",Примечание) VALUES (".implode(', ',array_map(array($this, 'PDO_quote'),array_values($value))).",REPLACE(".$this->PDO_quote($goods_type_string).",'@CRLF',CHAR(13)))",$wh_connection,true);
										$wh_goods_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_goods_table_name']."') AS int",$wh_connection)->fetchColumn();
										$order_barcodes = array_map(array($this, 'PDO_quote'),$order_barcodes);
										array_walk($order_barcodes, function(&$item) { $item = $item.')'; });
										$order_barcodes = "($wh_goods_id,".implode(",($wh_goods_id,",$order_barcodes);
										$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_barcodes_table_name']." (Грузы_Код,ШК) VALUES $order_barcodes",$wh_connection,true);
										break;
									}
								}
								else
								{
									$value['Тип_Груза_Код'] = $this->server_prm['unkown_goods_id'];
									if($value['Примечание'] != 'NULL')
										$value['Примечание'] = $value['Вид_груза'].':'.$value['Примечание'].'.@CRLF'.$goods_type_string.'. ';
									else
										$value['Примечание'] = $value['Вид_груза'].'.@CRLF'.$goods_type_string;
									unset($value['Кол_ШК']);
									unset($value['Вид_груза']);
									$goods_type_string = $value['Примечание'];
									unset($value['Примечание']);
									$value['Объем'] = $goods_volume_sum;
									$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_table_name']." (".implode(',',array_keys($value)).",Примечание) VALUES (".implode(', ',array_map(array($this, 'PDO_quote'),array_values($value))).",REPLACE(".$this->PDO_quote($goods_type_string).",'@CRLF',CHAR(13)))",$wh_connection,true);
									$wh_goods_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_goods_table_name']."') AS int",$wh_connection)->fetchColumn();
									$order_barcodes = array_map(array($this, 'PDO_quote'),$order_barcodes);
									array_walk($order_barcodes, function(&$item) { $item = $item.')'; });
									$order_barcodes = "($wh_goods_id,".implode(",($wh_goods_id,",$order_barcodes);
									$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_barcodes_table_name']." (Грузы_Код,ШК) VALUES $order_barcodes",$wh_connection,true);
									break;
								}
							}
						}
						else
						{
							$barcodes_amount = $value['Кол_ШК'];
							unset($value['Кол_ШК']);
							unset($value['Вид_груза']);
							$this->catchPDO("INSERT INTO ".$this->server_prm['wh_income_goods_table_name']." (".implode(',',array_keys($value)).") VALUES (".implode(', ',array_map(array($this, 'PDO_quote'),array_values($value))).")",$wh_connection,true);
							$wh_goods_id = $this->catchPDO("SELECT IDENT_CURRENT('".$this->server_prm['wh_income_goods_table_name']."') AS int",$wh_connection)->fetchColumn();
							$this->catchPDO("EXEC ".$this->server_prm['wh_income_barcodes_add_proc']." $wh_goods_id,0,$barcodes_amount",$wh_connection,true);
						}
					}
					$pver_owner_id = $owner_id;
				}
				$wh_connection->commit();
			}
		}
	}
}
?>
