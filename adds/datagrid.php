<?php
    namespace ON;

    class Datagrid
    {
        private $_table=array();
        private $_grid=NULL;
        private $_showactions=TRUE;
        private $_actions=array('delete', 'update');
        private $_actionstitle='Actions';
        private $_deleteurl='delete/%id%';
        private $_updateurl='update/%id%';
        private $_aditionaldeletelink='';
        private $_aditionalupdatelink='';
        private $_tableclass='';
        private $_updateclass='';
        private $_deleteclass='';
        private $_updatelabel='Update';
        private $_deletelabel='Delete';
        private $_adicionalactionhtml='';
        private $_norecordsfound='No Records Found';
        private $_fields=array();
        private $_headers=array();
        private $_dtfields=array();
        private $_mnfields=array();
        private $_keyfield='id';
        private $_base64id=false;
        private $_ttfields=null;
        private $_colorfields=array();

        public function __construct($table=array())
        {
          $this->_table=$table;
        }
        
        public function generate()
        {
          if(!is_null($this->_ttfields)):
            foreach($this->_ttfields as $field):
                $total[$field]=0;
            endforeach;  
          endif;
          if (sizeof($this->_table)>0):
            foreach ($this->_table as $reg):
              $id=NULL;
              if (is_object($reg))
                $reg=$reg->getFieldList();
              if (is_array($reg)):
                if (is_null($this->_grid)):
                  $this->_grid.='<table class="'.$this->_tableclass.'">';
                  $this->_grid.='<thead>';
                  $this->_grid.='<tr>';
                  if (sizeof($this->_headers)>0):
                      foreach ($this->_headers as $header):          
                        $this->_grid.='<th>';
                        $this->_grid.=$header;
                        $this->_grid.='</th>';
                      endforeach;
                  else:
                      foreach ($reg as $field=>$value):          
                        $this->_grid.='<th>';
                        $this->_grid.=ucwords($field);
                        $this->_grid.='</th>';
                      endforeach;
                  endif;
                  if ($this->_showactions):
                    $this->_grid.='<th>';
                    $this->_grid.=$this->_actionstitle;
                    $this->_grid.='</th>';
                  endif;
                  $this->_grid.='</tr>';
                  $this->_grid.='</thead>';
                  $this->_grid.='<tbody>';
                endif;
                $this->_grid.='<tr>';

                if (sizeof($this->_fields)>0):
                    if($this->_base64id):
                        $id=base64_encode($reg[$this->_keyfield]);
                    else:
                        $id=$reg[$this->_keyfield];
                    endif;
                    foreach ($this->_fields as $field):
                        if(!is_null($this->_ttfields)):
                            if(in_array($field, $this->_ttfields)):
                                $total[$field]+=(float)$reg[$field];
                            endif;
                        endif;
                        $this->_grid.='<td>';
                        if(in_array($field, $this->_dtfields)):
                            if(method_exists('\ON\Text', 'data')):
                                $reg[$field]=Text::datahora($reg[$field]);
                            endif;
                        endif;
                        if(in_array($field, $this->_mnfields)):
                            if(method_exists('\ON\Text', 'moeda')):
                                if(in_array($field, $this->_colorfields)):
                                    if(((float)$reg[$field])>=0):
                                        $reg[$field]='<span class="money positive">'.Text::moeda($reg[$field]).'</span>';
                                    else:
                                        $reg[$field]='<span class="money negative">'.Text::moeda($reg[$field]).'</span>';
                                    endif;
                                else:
                                    $reg[$field]=Text::moeda($reg[$field]);
                                endif;
                                
                            endif;
                        endif;
                        $this->_grid.=$reg[$field];
                        $this->_grid.='</td>';
            
                    endforeach;
                else:
                    if($this->_base64id):
                        $id=base64_encode($reg[$this->_keyfield]);
                    else:
                        $id=$reg[$this->_keyfield];
                    endif;
                    foreach ($reg as $field=>$value):
                        if(!is_null($this->_ttfields)):
                            if(in_array($field, $this->_ttfields)):
                                $total[$field]+=(float)$reg[$field];
                            endif;
                        endif;
                        $id=(is_null($id))?($this->_base64id?base64_encode($value):$value):$id;
            
                        if(in_array($field, $this->_mnfields)):
                            if(method_exists('ON\Text', 'moeda')):
                                if(in_array($field, $this->_colorfields)):
                                    if(((float)$value)>=0):
                                        $value='<span class="money positive">'.Text::moeda($value).'</span>';
                                    else:
                                        $value='<span class="money negative">'.Text::moeda($value).'</span>';
                                    endif;
                                else:
                                    $value=Text::moeda($value);
                                endif;
                            endif;
                        endif;
            
                        $this->_grid.='<td>';
                        $this->_grid.=$value;
                        $this->_grid.='</td>';
                    endforeach;
                endif;
                if ($this->_showactions):
                  $this->_grid.='<td>';
                  if (in_array('update', $this->_actions))
                    $this->_grid.='<a href="'.str_replace('%id%', $id, $this->_updateurl).'" class="'.$this->_updateclass.'" '.$this->_aditionalupdatelink.'>'.$this->_updatelabel.'</a> ';
                  if (in_array('delete', $this->_actions))
                    $this->_grid.='<a href="'.str_replace('%id%', $id, $this->_deleteurl).'" class="'.$this->_deleteclass.'" '.$this->_aditionaldeletelink.'>'.$this->_deletelabel.'</a>';
                  $this->_grid.=str_replace('%id%', $id, $this->_adicionalactionhtml);
                  $this->_grid.='</td>';
                endif;
                $this->_grid.='</tr>';
              endif;
            endforeach;
            $this->_grid.='</tbody>';
            
            if(!is_null($this->_ttfields)):
                $this->_grid.='<tfoot>';
                $this->_grid.='<tr>';         
                if (sizeof($this->_fields)>0):
                    foreach ($this->_fields as $field):
                        if(in_array($field, $this->_ttfields)):
                            if((in_array($field, $this->_mnfields))&&
                               (method_exists('ON\Text', 'moeda'))):
                                $this->_grid.='<td>'.Text::moeda($total[$field]).'</td>';
                            else:
                                $this->_grid.='<td>'.$total[$field].'</td>';
                            endif;
                        else:
                            $this->_grid.='<td></td>';
                        endif;
                    endforeach;
                else:
                    foreach ($reg as $field=>$value):
                        if(in_array($field, $this->_ttfields)):
                            if((in_array($field, $this->_mnfields))&&
                               (method_exists('ON\Text', 'moeda'))):
                                $this->_grid.='<td>'.Text::moeda($total[$field]).'</td>';
                            else:
                                $this->_grid.='<td>'.$total[$field].'</td>';
                            endif;
                        else:
                            $this->_grid.='<td></td>';
                        endif;
                    endforeach;
                endif;
                if ($this->_showactions):
                    $this->_grid.='<td></td>';
                endif;
                $this->_grid.='</tr>';
                $this->_grid.='</tfoot>';
            endif;
            $this->_grid.='</table>';
          else:
            $this->_grid=$this->_norecordsfound;
          endif;
          return $this->_grid;        
        }
        
        public function setTableClass($class)
        {
          $this->_tableclass=$class;
        }
        
        public function setUpdateClass($class)
        {
          $this->_updateclass=$class;
        }
        
        public function setDeleteClass($class)
        {
          $this->_deleteclass=$class;
        }
        
        public function setDeleteUrl($url)
        {
          $this->_deleteurl=$url;
        }
        
        public function setUpdateUrl($url)
        {
          $this->_updateurl=$url;
        }
        
        public function setAditionalHTMLDeleteLink($html)
        {
          $this->_aditionaldeletelink=$html;
        }
        
        public function setAditionalHTMLUpdateLink($html)
        {
          $this->_aditionalupdatelink=$html;
        }
        
        public function setDeleteLabel($label)
        {
          $this->_deletelabel=$label;
        }
        
        public function setUpdateLabel($label)
        {
          $this->_updatelabel=$label;
        }
        
        public function setNoRecordsFound($text)
        {
          $this->_norecordsfound=$text;
        }
        
        public function setAdictionalActionHTML($html)
        {
          $this->_adicionalactionhtml=$html;
        }
        
        public function setShowActions($showactions)
        {
          $this->_showactions=(bool)$showactions;
        }

        public function setActionsTitle($actionstitle)
        {
          $this->_actionstitle=$actionstitle;
        }

        public function setHeaders($headers)
        {
          $this->_headers=$headers;
        }
        
        public function setFields($fields)
        {
          $this->_fields=$fields;
        }
        
        public function setKeyField($key)
        {
          $this->_keyfield=$key;
        }
        
        public function setBase64Id($base)
        {
          $this->_base64id=(bool)$base;
        }
        
        public function setDateFields($dtfields)
        {
          $this->_dtfields=$dtfields;
        }
        
        public function setTotalizador($totalizador)
        {
          $this->_ttfields=$totalizador;
        }
        public function setMoneyFields($mnfields)
        {
          $this->_mnfields=$mnfields;
        }
        public function setColorFields($colorfields)
        {
          $this->_colorfields=$colorfields;
        }
  }