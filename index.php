<? require_once ($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include.php");

if(empty($_POST['arhistory'])){

    die();
}

use Bitrix\Main;
use Bitrix\Main\Loader;
use Optid\Local;
use Bitrix\Highloadblock as HL;

Loader::IncludeModule("highloadblock");
Loader::IncludeModule("optid.local");
Loader::IncludeModule("sadovod.local");
// print_r($_POST);

function getDiffColor($date_create){
	$date_cur = strtotime(date('d.m.Y'));
    $datediff = abs(floor(($date_create - $date_cur) / (60 * 60 * 24)));
    $daySec = 60 * 60 * 24;
    $sec = time() - $date_create;
    if ($datediff >= 5) {
        $color = '#d8d8d8';
    } elseif ($datediff == 4) {
        $color = '#ffa6ff';
    } elseif ($datediff == 3) {
        $color = '#faa3a3';
    } elseif ($datediff == 2) {
        $color = '#fed4b5';
    } elseif ($datediff == 1) {
        $color = '#ffee95';
    }else {
        $color = '#c6ffa0';
    }

	return $color;
}

	if($_POST['action'] == 'basket') {
		if(!empty($_POST['code']))
			$CODE = $_POST['code'];
		else
            $CODE = [ 'UF_QUANTITY_FACT','UF_CHECK','UF_BRAK_SORT', 'UF_BRAK_GAVE', 'UF_BRAK_NOTF','UF_SORT_FAKT','UF_UPAK_FAKT','UF_PRICE_BASE','PRICE','IB_PROVIDER','UF_MANAGER_COMMENT','UF_BRAK_MODEL','UF_BRAK_SIZE','UF_BRAK','UF_BRAK_REJECT','UF_BRAK_NOFOUND','UF_REPLACE_VALUE', 'UF_BRAK_SORT', 'UF_RECHECK', 'UF_NO', 'UF_NO_SIZE', 'UF_PROVIDER_ID', 'UF_CANNOT_BE_DEL', 'UF_REASONS'];
            
		foreach ($_POST['arhistory'] as $key => $value) {
			if(!empty($value)){
		    $history[$key] = Local\Hlblock\HistoryChanges::getList('ID',
			    ['filter'=>
			        [	'=UF_PROPERTY_CODE'=>$CODE,
			        	'UF_ZAKAZY_ID'	=>	$value
			    	],
			    'order'=>
			        ["ID"=>"ASC"]
			    ]);
			}
        }

		function getClass($clear=false){
			static $count = 0;
			if($clear) $count = 0;
			$arrColor = [
				'red_bg',
				'blue_bg',
				'green_bg',
				'orange_bg',
				'gray_bg',
				'sienna_bg',
				'violet_bg'
			];
			
			if($count == count($arrColor))
				$count = 0;
			$result = $arrColor[$count];
			$count ++;

			return $result;
        }

        function dateDiff($date_create)
		{
		    $date_cur = strtotime(date('d.m.Y'));
		    $datediff = abs(floor(($date_create - $date_cur) / (60 * 60 * 24)));
		    if ($datediff == 0) $date = date('H:i', $date_create);
		    else {
		        $daySec = 60 * 60 * 24;
		        $sec = time() - $date_create;
		        if ($datediff > 30) {
		            $date = floor($datediff / 30) . 'м';
		        } elseif ($datediff > 7) {
		            $date = floor($datediff / 7) . 'н';
		        } elseif ($datediff > 0) {
		            $date = $datediff . 'д';
		        } /*else {
		            $date = 'Вчера';
		        }*/
		    }
		    //$date .= ' - '.$datediff;
		    return $date;
		}

        function formatMyDate($dateObj, $date_time = false, $hover = false){
            if($dateObj->format('d.m.Y') == date('d.m.Y')){
                return $dateObj->format('H:i');
            }else{
            	if ($date_time)	{
            		if ($hover)
            			$r = $dateObj->format('d.m').'<span class="bl_hover">'.$dateObj->format('H:i').'</span>';
            		else $r = $dateObj->format('d.m H:i');
            	}	
            	else $r = $dateObj->format('d.m');
                return $r;
            }
        }
        // AddMessage2Log(print_r($history, true),'history');
		foreach ($history as $valHis) {
			foreach ($valHis as $key => $val) {
                if(empty($arrCollector[$val['UF_ZAKAZY_ID']])){
                    $arrCollector[$val['UF_ZAKAZY_ID']] = $val;
                }
                $arrUserCollector[$val['UF_ZAKAZY_ID']] = $val;
				$arrHistory[$val['UF_ZAKAZY_ID']][$val['UF_PROPERTY_CODE']][$key] = $val;
				$arrUser[$val['UF_USER_ID']] = $val['UF_USER_ID'];
				$count[$val['UF_ZAKAZY_ID']] ++;
                $arrDateCheck[$val['UF_ZAKAZY_ID']][$val['UF_PROPERTY_CODE']][$key] = $count[$val['UF_ZAKAZY_ID']];
                if($val['UF_PROPERTY_CODE'] == 'UF_PROVIDER_ID'){
                    $arrProviderSadovodId[$val['UF_VALUE_AFTER']] = $val['UF_VALUE_AFTER'];
                    $arrProviderSadovodId[$val['UF_VALUE_BEFORE']] = $val['UF_VALUE_BEFORE'];
                }
			}
		}
       
		foreach ($arrHistory as $idZak0 => $vid0) {
			$class = '';
			getClass(1);
			foreach ($vid0 as $CODE0 => $vVal10) {
				$class = '';
				getClass(1);
				if($CODE0 == 'UF_QUANTITY_FACT'){
					// foreach ($vVal10 as $keyDay0 => $valDay0) {

						foreach ($vVal10 as $idProp => $props) {
					        	if(empty($class)){
					        		$class = '';//getClass();
					        	}
							if(!$arrCount[$idZak0][$props['UF_DATE']->format('d.m.Y')])$arrCount[$idZak0][$props['UF_DATE']->format('d.m.Y')] = 0;
							if(!$countSobr[$idZak0][$props['UF_DATE']->format('d.m.Y')])$countSobr[$idZak0][$props['UF_DATE']->format('d.m.Y')]['COUNT'] = [];

							if($props['UF_SOURCE'] == 'zakazy_new'){
						
								$countSobr[$idZak0][$props['UF_DATE']->format('d.m.Y')]['COUNT'][$arrCount[$idZak0][$props['UF_DATE']->format('d.m.Y')]+1] = $props['UF_VALUE_AFTER'];
								$countSobr[$idZak0][$props['UF_DATE']->format('d.m.Y')]['CLASS'] = $class;

								
							}elseif($props['UF_SOURCE'] == 'sklad'){
								
								$arrCount[$idZak0][$props['UF_DATE']->format('d.m.Y')]['COUNT'] = count($countSobr[$idZak0][$props['UF_DATE']->format('d.m.Y')]['COUNT']);
								$countSobr[$idZak0][$props['UF_DATE']->format('d.m.Y')]['CLASS2'] = $class;
								$class = '';

							}
						}
					// }
					$arrHistory[$idZak0]['COUNT_SOBR'] = $countSobr[$idZak0];

				}elseif($CODE0 == 'UF_BRAK_MODEL' || $CODE0 == 'UF_BRAK_SIZE' || $CODE0 == 'UF_BRAK' || $CODE0 == 'UF_BRAK_REJECT' || $CODE0 == 'UF_BRAK_NOFOUND' || $CODE0 == 'UF_CHECK' || $CODE0 == 'UF_BRAK_SORT' || $CODE0 == 'UF_RECHECK' || $CODE0 == 'UF_SORT_FAKT' || $CODE0 == 'UF_UPAK_FAKT'){


						foreach ($vVal10 as $idProp => $props) {
							if(!$countTransfer[$idZak0][$CODE0]['COUNT'])$countTransfer[$idZak0][$CODE0]['COUNT'] = 0;
							$countTransfer[$idZak0][$CODE0]['COUNT'] = $props['UF_VALUE_AFTER'];
						}
								
					$arrHistory[$idZak0]['COUNT_TRANSFER'][$CODE0] = $countTransfer[$idZak0][$CODE0];			
				}
			}
		}


		if(!empty($arrHistory)){
            if(!empty($arrUser)){
                $resUser = \Bitrix\Main\UserTable::getList(array(
                    'select' => array('ID','LOGIN'),
                    'filter' => ['ID'=> $arrUser]
                ));
                while ($arUser = $resUser->fetch()) {
                    $arUsers[$arUser['ID']] = $arUser;
                }
            }
            if(!empty($arrProviderSadovodId)){
                $providerSadovod =  \Sadovod\Local\Hlblock\Provider::getList('UF_ID_SADOVOD', ['select' => ['UF_XML_ID', 'ID', 'UF_NAME', 'UF_ID_SADOVOD'], 'filter' => ['UF_ID_SADOVOD' => $arrProviderSadovodId]]);
            }

			foreach ($arrHistory as $idZak => $vid) {
                
				$class = '';
				getClass(1);
				
				foreach ($vid as $CODE => $vVal1) {
					if($CODE == 'COUNT_SOBR' || $CODE == 'COUNT_TRANSFER') continue;
												
						$class = '';

						getClass(1);
                        
                        $l_sob = 5; $min_str = false;
						if ($_POST['info_view'] == 'min') {
							$l_sob = 3; $min_str = true;
						}
                        
                        $lastDate = ''; $i = 0;
                        $vVal1 = array_reverse($vVal1);
						foreach ($vVal1 as $idVal => $vVal) {
                            // if($arrUserCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_AFTER'] > 1){
                                if($arrUserCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_AFTER'] > $vVal['UF_VALUE_AFTER']){
                                    continue;
                                }
                                
                            // }elseif($arrUserCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_AFTER'] <= 1 && strtotime($vVal['UF_DATE']->format('d.m.Y')) == strtotime(date('d.m.Y')) && $arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_USER_ID'] == $vVal['UF_USER_ID']){
                            //     if($arrUserCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_AFTER'] > $vVal['UF_VALUE_AFTER']){
                            //         continue;
                            //     }
                            // }
                           
					        $user = (!empty($arUsers[$vVal['UF_USER_ID']]['LOGIN']))?$arUsers[$vVal['UF_USER_ID']]['LOGIN']:$vVal['UF_USER_ID'];
					        if(!$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']]) $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] = '';
					        
                            
                            //Разделяем полосой если разные даты рядом 
                            $curDate = $vVal['UF_DATE']->format('d.m.Y');
                            if (empty($lastDate)) $lastDate = $vVal['UF_DATE']->format('d.m.Y');
                            if ($lastDate != $curDate) {
                            	//$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="line_history"></div>';
                            	$lastDate = $curDate;
                            }
                            
                            //Показываем сразу только последние 4 действия
                            $i++;
                            /*if (count($vVal1) == 4) {
                            	if ((count($vVal1)-3) == $i) $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="hide_history">';
                      
                            }*/
                            
                           
                           
                            if(strtotime($vVal['UF_DATE']->format('d.m.Y')) == strtotime(date('d.m.Y')) && $arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_USER_ID'] == $vVal['UF_USER_ID']){
                                if ($i == 1) {
                                    $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="block_history">';
                                }
                                if ($i == 2) {
                                    $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="hide_history">';
                                }
                                if(count($vVal1) > 1 && $i == count($vVal1)){
                                    $colorClass = 'oneUser red_bg white';
                                }
                                
                            }else{
                                if ($i == 1) {
                                    $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="block_history">';
                                }
                                if ($i == 5) {
                                    $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="hide_history">';
                                }
                            }
                            
                            
                           
						    $color = getDiffColor(strtotime($vVal['UF_DATE']));
                            
                            $date_t_h = '<span class="bl_hover">'.((!$min_str)?mb_substr($vVal['UF_SOURCE'],0,3).'&nbsp;&nbsp;':'').' '.$vVal['UF_DATE']->format('H:i:s').'&nbsp;&nbsp;'.$vVal['UF_DATE']->format('d.m').'</span>';
                            if (date('d.m.Y') != $vVal['UF_DATE']->format('d.m.Y')) {
				        	    $date_t_h = '('.( dateDiff(strtotime($vVal['UF_DATE'])) ).')'.$date_t_h;
                            }

                            $l_sob_2 = $l_sob;
							if (in_array($user, ['Dil','saodat','Nar'])) {
								$l_sob_2 = 1;
								$user['0'] = strtoupper($user['0']);
							}
                            $login_str = '<span class="login_'/*.$user*/.'">'.mb_substr($user, 0, $l_sob_2).'</span>';

					        if($vVal['UF_PROPERTY_CODE'] == 'UF_QUANTITY_FACT'){
                                if(strtotime($vVal['UF_DATE']->format('d.m.Y')) == strtotime(date('d.m.Y')) && $arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_USER_ID'] == $vVal['UF_USER_ID']){
                                    if(!$y[$idZak][$vVal['UF_DATE']->format('d.m.Y')]) $y[$idZak][$vVal['UF_DATE']->format('d.m.Y')] = 1;
                                
                                    //показываем в формате 1д,2д,3д с наведением и если сегодня, вообще не выводим 
                                    
                                    if($vVal['UF_VALUE_AFTER']>=$vVal['UF_VALUE_BEFORE']){
                                        if(empty($class)){
                                            $x = true;
                                        }
                                        //((!$min_str)?'('.$arrDateCheck[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']][$vVal['ID']].') ':'') .
                                        if($x && !empty($vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['COUNT'][$y[$idZak][$vVal['UF_DATE']->format('d.m.Y')]])){
                                            $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 1 '.$colorClass.'" style="background:'.$color.'">'.'<span class="'.$vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['CLASS'].'">'.$login_str.' '.$arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                            $x = false;
                                            $y[$idZak][$vVal['UF_DATE']->format('d.m.Y')] ++;
                                        }
                                        // else{
                                        //     $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 2 '.$colorClass.'" style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                        // }
                                    }elseif($vVal['UF_VALUE_AFTER']<$vVal['UF_VALUE_BEFORE']){
                                        $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 3 '.$colorClass.'"  style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                        if($vVal['UF_SOURCE'] == 'sklad')$class = '';	
                                            
                                    }
                                }elseif(strtotime($vVal['UF_DATE']->format('d.m.Y')) == strtotime(date('d.m.Y')) && $arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_USER_ID'] != $vVal['UF_USER_ID']){
                                    if(!$y[$idZak][$vVal['UF_DATE']->format('d.m.Y')]) $y[$idZak][$vVal['UF_DATE']->format('d.m.Y')] = 1;
                                
                                    //показываем в формате 1д,2д,3д с наведением и если сегодня, вообще не выводим 
                                    
                                    if($vVal['UF_VALUE_AFTER']>=$vVal['UF_VALUE_BEFORE']){
                                        if(empty($class)){
                                            $x = true;
                                        }
                                        //((!$min_str)?'('.$arrDateCheck[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']][$vVal['ID']].') ':'') .
                                        if($x && !empty($vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['COUNT'][$y[$idZak][$vVal['UF_DATE']->format('d.m.Y')]])){
                                            $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 1" style="background:'.$color.'">'.'<span class="'.$vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['CLASS'].'">'.$login_str.' '.$arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_AFTER'].'->'.$arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                            $x = false;
                                            $y[$idZak][$vVal['UF_DATE']->format('d.m.Y')] ++;
                                        }
                                        else{
                                            $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 2" style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_AFTER'].'->'.$arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                        }
                                    }elseif($vVal['UF_VALUE_AFTER']<$vVal['UF_VALUE_BEFORE']){
                                        // $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 3 '.$colorClass.'"  style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                        // if($vVal['UF_SOURCE'] == 'sklad')$class = '';	
                                            
                                    }
                                }else{
                                    if(!$y[$idZak][$vVal['UF_DATE']->format('d.m.Y')]) $y[$idZak][$vVal['UF_DATE']->format('d.m.Y')] = 1;
                            
                                    //показываем в формате 1д,2д,3д с наведением и если сегодня, вообще не выводим 
                                    
                                    if($vVal['UF_VALUE_AFTER']>=$vVal['UF_VALUE_BEFORE']){
                                        if(empty($class)){
                                            $x = true;
                                        }
                                        //((!$min_str)?'('.$arrDateCheck[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']][$vVal['ID']].') ':'') .
                                        if($x && !empty($vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['COUNT'][$y[$idZak][$vVal['UF_DATE']->format('d.m.Y')]])){
                                            $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 1" style="background:'.$color.'">'.'<span class="'.$vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['CLASS'].'">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                            $x = false;
                                            $y[$idZak][$vVal['UF_DATE']->format('d.m.Y')] ++;
                                        }else{
                                            $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 2" style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                        }
                                    }elseif($vVal['UF_VALUE_AFTER']<$vVal['UF_VALUE_BEFORE']){
                                        $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 3"  style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
                                        if($vVal['UF_SOURCE'] == 'sklad')$class = '';	
                                             
                                    }
                                }
                                
                                
						    }elseif($vVal['UF_PROPERTY_CODE'] == 'UF_BRAK_MODEL' || $vVal['UF_PROPERTY_CODE'] == 'UF_BRAK_SIZE' || $vVal['UF_PROPERTY_CODE'] == 'UF_BRAK' || $vVal['UF_PROPERTY_CODE'] == 'UF_BRAK_REJECT' || $vVal['UF_PROPERTY_CODE'] == 'UF_BRAK_NOFOUND'){

						    	
						        if($vVal['UF_VALUE_AFTER']>=$vVal['UF_VALUE_BEFORE']){
						        	$x = true;
						        	
						        	if($x && !empty($vid['COUNT_TRANSFER'][$vVal['UF_PROPERTY_CODE']]['COUNT'])){
						        		$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 5 '.$colorClass.'" style="background:'.$color.'">'.'<span class="'.$vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['CLASS2'].'">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
						        		$x = false;
						        	}else{
						        		$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 6 '.$colorClass.'" style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
						        	}
						        }elseif($vVal['UF_VALUE_AFTER']<$vVal['UF_VALUE_BEFORE']){
						        	$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 7 '.$colorClass.'" style="background:'.$color.'">' .'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';		        	
						        }
						    }elseif($vVal['UF_PROPERTY_CODE'] == 'UF_CHECK' || $vVal['UF_PROPERTY_CODE'] == 'UF_BRAK_SORT' || $vVal['UF_PROPERTY_CODE'] == 'UF_RECHECK' || $vVal['UF_PROPERTY_CODE'] == 'UF_SORT_FAKT' || $vVal['UF_PROPERTY_CODE'] == 'UF_UPAK_FAKT'){
						    	
						        if($vVal['UF_VALUE_AFTER']>=$vVal['UF_VALUE_BEFORE']){
						        	$x = true;
						        	
						        	if($x){
						        		$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 8 '.$colorClass.'" style="background:'.$color.'">'.'<span class="'.$vid['COUNT_SOBR'][$vVal['UF_DATE']->format('d.m.Y')]['CLASS2'].'">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
						        		$x = false;
						        	}else{
						        		$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 9 '.$colorClass.'" style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';
						        	}
						        }elseif($vVal['UF_VALUE_AFTER']<$vVal['UF_VALUE_BEFORE']){
                                    $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="l_hist date_time_hover 10 '.$colorClass.'" style="background:'.$color.'">'.'<span class="">'.$login_str.' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].' '.$date_t_h.'</span></div>';		        
                                    	
                                }

                            }elseif($vVal['UF_PROPERTY_CODE'] == 'UF_PROVIDER_ID' ){
                                
                                $sign = (empty($vVal['UF_VALUE_BEFORE']))?'->':'=';
                                $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<span class="1 '.$colorClass.'">'.$providerSadovod[$vVal['UF_VALUE_BEFORE']]['UF_NAME'].' '.$sign.' '.$providerSadovod[$vVal['UF_VALUE_AFTER']]['UF_NAME'].' ('.$vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, $l_sob).' '.')</span><br>';
                     
						    }elseif($vVal['UF_PROPERTY_CODE'] == 'UF_RETURN_COL' ){
                                
                                $sign = (empty($vVal['UF_VALUE_BEFORE']))?'->':'=';
                                $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<span class="2 '.$colorClass.'"> ('.$vVal['UF_DATE']->format('d.m H:i').' '.mb_substr($user, 0, $l_sob).' '.')</span><br>';
                     
						    }else{
						    	//((!$min_str)?'('.$arrDateCheck[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']][$vVal['ID']].') ':'')
						    	//((!$min_str)?mb_substr($vVal['UF_SOURCE'],0,3).' ':'')
					    		$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '<span class="date_time_hover"><span class="3 '.$colorClass.'">'.$vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, $l_sob).' '.$vVal['UF_VALUE_BEFORE'].'->'.$vVal['UF_VALUE_AFTER'].'</span><span class="bl_hover">'.$vVal['UF_DATE']->format('H:i').'</span></span><br>';
						    }

                            //Кнопка для показа остальных действий (сразу только последние 4 действия)
                            /*if (count($vVal1) > 4 && $i == count($vVal1)) {
                            	$Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '</div><div class="show_all_history">...</div>';
                            }*/
                            if(strtotime($vVal['UF_DATE']->format('d.m.Y')) == strtotime(date('d.m.Y')) && $arrCollector[$vVal['UF_ZAKAZY_ID']]['UF_USER_ID'] == $vVal['UF_USER_ID']){
                                if (count($vVal1) > 1 && $i == count($vVal1)) {
                                    $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '</div><div class="show_all_history">...</div>';
                                }
                            }else{
                                if (count($vVal1) > 4 && $i == count($vVal1)) {
                                    $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '</div><div class="show_all_history">...</div>';
                                }
                            }
                           
                            if ($i == count($vVal1)) {
						        $Result[$vVal['UF_ZAKAZY_ID']][$vVal['UF_PROPERTY_CODE']] .= '</div>';
                            }
                           
                           
				    	}
				    // 
			    }
		    }
           

		    if(!empty($Result)) echo \Bitrix\Main\Web\Json::encode($Result); 

		   
        }
        
    }elseif($_POST['action'] == 'provider'){

        $arhistory = array_filter($_POST['arhistory']);
        if(!empty($arhistory)){
            $history = Local\Hlblock\HistoryChanges::getList('ID',
                ['filter'=>
                    [
                        'UF_PROV_ID' =>	$arhistory,
                        '=UF_PROPERTY_CODE' => ['UF_COLLECTOR', 'UF_NAME'],
                ],
                'order'=>
                    ["UF_DATE"=>"DESC"]
                ]);    
        }       

        if(!empty($history)) {
            $users = array_merge(array_filter(array_unique(array_column($history, 'UF_USER_ID'))), array_filter(array_unique(array_column($history, 'UF_VALUE_BEFORE'))),  array_filter(array_unique(array_column($history, 'UF_VALUE_AFTER'))));
            $resUser = \Bitrix\Main\UserTable::getList(array(
                'select' => array('ID','LOGIN'),
                'filter' => ['ID'=> $users]
            ));


			while ($arUser = $resUser->fetch()) {
                $arUsers[$arUser['ID']] = $arUser;
            }

            $i_COLLECTOR = 0;
            $history = array_reverse($history);
            foreach ($history as $kid => $vid) {
                $user = '';
                $user = (!empty($arUsers[$vid['UF_USER_ID']]['LOGIN']))?$arUsers[$vid['UF_USER_ID']]['LOGIN']:$vid['UF_USER_ID'];

                if(!$Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']])
                    $Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] = ' ';
                
            

                if($vid['UF_PROPERTY_CODE'] == 'UF_COLLECTOR'){
                	$i_COLLECTOR++;
                	if ($i_COLLECTOR == 1) {
				        //$Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] .= '<div class="block_history">';
                    }
                    if ($i == 3) {
                    	//$Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] .= '<div class="hide_history">';
                    }
                    $Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] .= '<div class="l_hist">'.$vid['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$arUsers[$vid['UF_VALUE_BEFORE']]['LOGIN'].' -> '.$arUsers[$vid['UF_VALUE_AFTER']]['LOGIN'].'</div>';
                    if (count($vid) > 2 && $i_COLLECTOR == count($vid)) {
                    	//$Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] .= '</div><div class="show_all_history">...</div>';
                    }

                    if ($i_COLLECTOR == count($vid)) {
				        //$Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] .= '</div>';
                    }
                }elseif($vid['UF_PROPERTY_CODE'] == 'UF_NAME'){
                    $Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] .= $vid['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$vid['UF_VALUE_BEFORE'].' -> '.$vid['UF_VALUE_AFTER'].' / ';
                    $Result[$vid['UF_PROV_ID']]['LOGIN'] = $user;
                }else{
                    $Result[$vid['UF_PROV_ID']][$vid['UF_PROPERTY_CODE']] .= '<div>'.$vid['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$vid['UF_VALUE_BEFORE'].' -> '.$vid['UF_VALUE_AFTER'].'</div>'; 
                }
            }
            
        }
        if(!empty($Result)) echo \Bitrix\Main\Web\Json::encode($Result); 

	}elseif($_POST['action'] == 'order'){     
		foreach ($_POST['arhistory'] as $key => $value) {
			if(!empty($value)){
			    $historyOrder[$key] = Local\Hlblock\HistoryChanges::getList('ID',
				    ['filter'=>
				        [
				        	'UF_ORDER_ID'=>	$value,
				        	'UF_SITE'=>	$key,
			        	    '=UF_PROPERTY_CODE'=>['UF_CLIENT_NAME','UF_PASPORT','UF_PHONE', 'UF_ZIP','UF_DELIVERY_ID','UF_ADMINCOMMENT','UF_CLIENT_COMMENT', 'UF_UPAK_SEND', 'UF_TOOK', 'UF_STATUS_SEND', 'UF_TRACK_NUMBER', 'UF_DOC', 'UF_STATUS', 'PRINT_ORDER', 'UF_DRIVER_STATUS', 'UF_MESTO', 'UF_PRICE_BASE'],
				    ],
				    'order'=>
				        ["UF_DATE"=>"ASC"]
				    ]);
			}			
		}

		$arStatus = Local\Hlblock\Status::getList('UF_ID', ['order'=>["UF_SORT"=>"ASC"]]);
        
		if(!empty($historyOrder)){
			$arrDelivery = Local\Hlblock\Delivery::getList('ID', ['order'=>["ID"=>"ASC"]]);
			foreach($arrDelivery as $arItem) {
				$delivery[$arItem["UF_SITE"]][$arItem["UF_ORIGIN_ID"]] = $arItem['UF_NAME'];
			}
			
			foreach ($historyOrder as $kid => $vid) {

				$resUser = \Bitrix\Main\UserTable::getList(array(
				    'select' => array('ID','LOGIN'),
				    'filter' => ['ID'=> array_unique(array_column($vid, 'UF_USER_ID'))]
				));

				while ($arUser = $resUser->fetch()) {
					if($arUser['ID'] == 10 )$arUser['LOGIN'] = 'Клиент';
				     $arUsers[$arUser['ID']] = $arUser;

				}

				foreach ($vid as $kVal => $vVal) {
			      
	                $user = (!empty($arUsers[$vVal['UF_USER_ID']]['LOGIN']))?$arUsers[$vVal['UF_USER_ID']]['LOGIN']:$vVal['UF_USER_ID'];

	                if($vVal['UF_PROPERTY_CODE'] == 'UF_CLIENT_NAME'){

	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS']) $Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] = ' ';

	                	$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] .= 'Ф.И.О.: '.$vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$vVal['UF_VALUE_BEFORE'].' -> '.$vVal['UF_VALUE_AFTER'].'<br>';
	                }
	                elseif($vVal['UF_PROPERTY_CODE'] == 'UF_PASPORT'){
	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS']) $Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] = ' ';

	                	$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] .= 'Паспорт: '.$vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$vVal['UF_VALUE_BEFORE'].' -> '.$vVal['UF_VALUE_AFTER'].'<br>';
	                }
	                elseif($vVal['UF_PROPERTY_CODE'] == 'UF_PHONE'){
	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS']) $Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] = ' ';

	                	$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] .= 'Тел: '.$vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$vVal['UF_VALUE_BEFORE'].' -> '.$vVal['UF_VALUE_AFTER'].'<br>';
	                }
	                elseif($vVal['UF_PROPERTY_CODE'] == 'UF_ZIP'){
	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS']) $Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] = ' ';

	                	$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] .= 'Индекс: '.$vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$vVal['UF_VALUE_BEFORE'].' -> '.$vVal['UF_VALUE_AFTER'].'<br>';
	                }	                
	                elseif($vVal['UF_PROPERTY_CODE'] == 'UF_DELIVERY_ID'){
	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS']) $Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] = ' ';

	                	$Result[$kid][$vVal['UF_ORDER_ID']]['ADDRESS'] .= 'Доставка: '.$vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$delivery[$vVal['UF_SITE']][$vVal['UF_VALUE_BEFORE']].' -> '.$delivery[$vVal['UF_SITE']][$vVal['UF_VALUE_AFTER']].'<br>';
	                }
	                elseif($vVal['UF_PROPERTY_CODE'] == 'UF_STATUS'){
	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']]) $Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] = ' ';
                        
	                	$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] .= $vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$arStatus[$vVal['UF_VALUE_BEFORE']]['UF_SHORTNAME'].' -> '.$arStatus[$vVal['UF_VALUE_AFTER']]['UF_SHORTNAME'].' '.$vVal['UF_DATE']->format('H:i').'<br>';	
	                }
	                elseif($vVal['UF_PROPERTY_CODE'] == 'PRINT_ORDER'){
	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']]) $Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] = ' ';

	                	$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] .= $vVal['UF_DATE']->format('d.m H:i').' '.mb_substr($user, 0, 5).'<br>';	
	                	$Result[$kid][$vVal['UF_ORDER_ID']]['PRINT_ORDER_DIFF_DATE'] = abs(floor((strtotime($vVal['UF_DATE']->format('d.m.Y')) - strtotime(date('d.m.Y'))) / (60 * 60 * 24)));
                    }
                    elseif($vVal['UF_PROPERTY_CODE'] == 'UF_DRIVER_STATUS'){ 
                        $str = [109 => 'принял', 110 => 'отправил',];

                        if(!$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']]) $Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] = ' ';
	                	$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] .= $vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$str[$vVal['UF_VALUE_BEFORE']].' -> '.$str[$vVal['UF_VALUE_AFTER']].'<br>';
                    }
                    else{
	                	if(!$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']]) $Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] = ' ';
	                	$Result[$kid][$vVal['UF_ORDER_ID']][$vVal['UF_PROPERTY_CODE']] .= $vVal['UF_DATE']->format('d.m H:i').' '.mb_substr($user, 0, 5).' '.$vVal['UF_VALUE_BEFORE'].' -> '.$vVal['UF_VALUE_AFTER'].'<br>';
                    }
                    
                    
	            }		    	
		    }
		    
		    if(!empty($Result)) echo \Bitrix\Main\Web\Json::encode($Result); 	   
		}	    
	}elseif($_POST['action'] == 'element'){

		$historyEl = Local\Hlblock\HistoryChanges::getList('ID',
		    ['filter'=>
		        [
		        	'UF_ELEMENT_ID'=> array_unique($_POST['arhistory']),
		        	'=UF_PROPERTY_CODE'=>['ACTIVE', 'SOLD', 'IBLOCK_ID','NAME','PREVIEW_TEXT','QUANTITY_PRODUCT','EXTRA','PRICE_BASE','PRICE','PACK_NUM','IB_PROVIDER','BRAND','NO_SIZE', 'SIZE_NEW']
		        ],
		    'order'=>
		        ["UF_DATE"=>"ASC"]
            ]);       
		if(!empty($historyEl)){
	
			$resUser = \Bitrix\Main\UserTable::getList(array(
			    'select' => array('ID','LOGIN'),
			    'filter' => ['ID'=> array_unique(array_column($historyEl, 'UF_USER_ID'))]
			));
			while ($arUser = $resUser->fetch()) {
			    $arUsers[$arUser['ID']] = $arUser;
			}

			foreach ($historyEl as $kVal => $vVal) {

		        $user = (!empty($arUsers[$vVal['UF_USER_ID']]['LOGIN']))?$arUsers[$vVal['UF_USER_ID']]['LOGIN']:$vVal['UF_USER_ID'];
		        $user = mb_substr($user, 0, 5);

		        if(!$Result[$vVal['UF_ELEMENT_ID']][$vVal['UF_PROPERTY_CODE']])$Result[$vVal['UF_ELEMENT_ID']][$vVal['UF_PROPERTY_CODE']] = '';

		        if($vVal['UF_PROPERTY_CODE'] == 'SIZE_NEW'){
		        	if(!is_array($Result[$vVal['UF_ELEMENT_ID']]['SIZE_NEW'])){
		        		$Result[$vVal['UF_ELEMENT_ID']]['SIZE_NEW'] = [];
		        	}
		        	$Result[$vVal['UF_ELEMENT_ID']]['SIZE_NEW'][$vVal['UF_VALUE_BEFORE']]['SIZE'] = $vVal['UF_VALUE_BEFORE'];
		        	$Result[$vVal['UF_ELEMENT_ID']]['SIZE_NEW'][$vVal['UF_VALUE_BEFORE']]['VAL'][] = $vVal['UF_VALUE_AFTER'].' '.$user.' '.$vVal['UF_DATE']->format('d.m').' '.$vVal['UF_ORDER_ID'];

		        	if(!empty($vVal['UF_VALUE_AFTER'])){
		        		$Result[$vVal['UF_ELEMENT_ID']]['SIZE_NEW'][$vVal['UF_VALUE_BEFORE']]['SIZE_NEW_LAST'] = $vVal['UF_VALUE_AFTER'].' '.$user.' '.$vVal['UF_DATE']->format('d.m');
		        	}else{
		        		$Result[$vVal['UF_ELEMENT_ID']]['SIZE_NEW'][$vVal['UF_VALUE_BEFORE']]['SIZE_NEW_LAST'] = 0;
		        	}
		        	
		        }else{
		        	$Result[$vVal['UF_ELEMENT_ID']][$vVal['UF_PROPERTY_CODE']] .= '<div class="date_time_hover"><span class="el 1">'.$vVal['UF_DATE']->format('d.m').' '.$user.' '.$vVal['UF_VALUE_BEFORE'].' -> '.$vVal['UF_VALUE_AFTER'].'</span><span class="bl_hover">'.$vVal['UF_DATE']->format('H:i').'</span></div>';
		        }
		    	
            }
                     
	    	if(!empty($Result)) echo \Bitrix\Main\Web\Json::encode($Result);
		}

	}elseif($_POST['action'] == 'replace'){
		$historyEl = Local\Hlblock\HistoryChanges::getList('ID',
		    ['filter'=>
		        [
		        	'UF_ZAKAZY_ID'=> array_unique($_POST['arhistory']),
		        	'=UF_PROPERTY_CODE'=>'REPLACE_PHOTO_UF_STATUS'
		        ],
		    'order'=>
		        ["UF_DATE"=>"ASC"]
		    ]);
		if(!empty($historyEl)){
	
			$resUser = \Bitrix\Main\UserTable::getList(array(
			    'select' => array('ID','LOGIN'),
			    'filter' => ['ID'=> array_unique(array_column($historyEl, 'UF_USER_ID'))]
			));
			while ($arUser = $resUser->fetch()) {
			     $arUsers[$arUser['ID']] = $arUser;
			}
			foreach ($historyEl as $kVal => $vVal) {

		        $user = (!empty($arUsers[$vVal['UF_USER_ID']]['LOGIN']))?$arUsers[$vVal['UF_USER_ID']]['LOGIN']:$vVal['UF_USER_ID'];
		        if(!$Result[$vVal['UF_ZAKAZY_ID']]['REPLACE'])$Result[$vVal['UF_ZAKAZY_ID']]['REPLACE'] = '';

		    	$Result[$vVal['UF_ZAKAZY_ID']]['REPLACE'] .= $vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' '.$vVal['UF_VALUE_BEFORE'].' -> '.$vVal['UF_VALUE_AFTER'].'<br>';
	    	}

	    	if(!empty($Result)) echo \Bitrix\Main\Web\Json::encode($Result);
		}		
	}elseif($_POST['action'] == 'sending'){

		$provList = Local\Hlblock\Provider::getList('ID', ['select'=>['UF_NAME', 'UF_XML_ID', 'ID']]);

		$hlblock = HL\HighloadBlockTable::getList(array('filter' => array('=TABLE_NAME' => 'Returns')))->fetch();
		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entityClass = $entity->getDataClass();

		// $filter['<UF_DATE'] = $this->bDateTo;
		$filter['>=UF_DATE'] = date("d.m.Y H:i:s", mktime(0, 0, 0, date("m")-1, date("d")));;

		$res = $entityClass::getList(array(
		   'select' => array('*'),
		   'order' => array('ID' => 'ASC'),
		   'filter' => array($filter)
		));

		while ($row = $res->fetch()) {
			$row['UF_PROVIDER'] = $provList[$row['UF_PROVIDER']]['UF_XML_ID'];
			$arHistory[] = $row;
		}


		if(!empty($arHistory)){
	
			$resUser = \Bitrix\Main\UserTable::getList(array(
			    'select' => array('ID','LOGIN'),
			    'filter' => ['ID'=> array_unique(array_column($arHistory, 'UF_USER_ID'))]
			));
			while ($arUser = $resUser->fetch()) {
			     $arUsers[$arUser['ID']] = $arUser;
			}

			foreach ($arHistory as $kVal => $vVal) {

		        $user = (!empty($arUsers[$vVal['UF_USER_ID']]['LOGIN']))?$arUsers[$vVal['UF_USER_ID']]['LOGIN']:$vVal['UF_USER_ID'];
	
		    	$Result[$vVal['UF_PROVIDER']]['SENDING'] .= $vVal['UF_DATE']->format('d.m').' '.mb_substr($user, 0, 5).' -> '.$vVal['UF_VALUE'].'<br>';
	    	}

	    	if(!empty($Result)) echo \Bitrix\Main\Web\Json::encode($Result);
		}		
	}



