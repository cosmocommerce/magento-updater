<?php
class CosmoCommerce_Updates_Block_Adminhtml_System_Config_Form_Field_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    
    
        $_htmlId = $element->getHtmlId();
        // Get the default HTML for this option
        $html = parent::_getElementHtml($element);

        $base_path = Mage::getBaseDir('base');
        $modman_path = Mage::getBaseDir('base').DS.'.modman';
        
        $html="";
        if (!extension_loaded("git2")){
            $html='<button id="check_version" title="模块环境缺失" type="button" class="scalable save"  style=""><span><span><span>模块环境缺失</span></span></span></button>';
        }else{
            $html='<button id="check_version" title="模块环境正常" type="button" class="scalable save"  style=""><span><span><span>模块环境正常</span></span></span></button>';
           
            
            
            /*
            $html.="<br />";
            chdir(Mage::getBaseDir('base'));
            $last_line = exec('/var/www/bin/modman status', $output,$retval);
            $html.=implode("<br />",$output);
            */
            
            
            $html.="<ul style='font-size: 11px;'>";     
            $html.='            
            <script type="text/javascript">
            //<![CDATA[
            function redirectToUpdate()
            {
                console.log(this.value);
                var url = "'.Mage::getSingleton("adminhtml/url")->getUrl("*/updates/update").'"+"?repo="+this.value;
                if (confirm("模块将会更新代码.")) {
                    if (Prototype.Browser.IE) {
                        var generateLink = new Element("a", {href: url});
                        $$("body")[0].insert(generateLink);
                        generateLink.click();
                    } else {
                        window.location.href = url;
                    }
                }
            }
            function redirectToUpdatef()
            {
                console.log(this.value);
                var url = "'.Mage::getSingleton("adminhtml/url")->getUrl("*/updates/updatef").'"+"?repo="+this.value;
                if (confirm("模块将会强制覆盖代码.")) {
                    if (Prototype.Browser.IE) {
                        var generateLink = new Element("a", {href: url});
                        $$("body")[0].insert(generateLink);
                        generateLink.click();
                    } else {
                        window.location.href = url;
                    }
                }
            }
            function redirectToCommit()
            {
                console.log(this.value);
                var url = "'.Mage::getSingleton("adminhtml/url")->getUrl("*/updates/commit").'"+"?repo="+this.value;
                var note = prompt("请输入记录这次版本的备注");
                var user = prompt("请输入用户名");
                var pass = prompt("请输入密码");
                url=url+"&note="+note;
                url=url+"&pass="+pass;
                url=url+"&user="+user;
                if (confirm("模块更新将会进行提交.")) {
                    if (Prototype.Browser.IE) {
                        var generateLink = new Element("a", {href: url});
                        $$("body")[0].insert(generateLink);
                        generateLink.click();
                    } else {
                        window.location.href = url;
                    }
                }
            }

            function disableGenerateButton(id)
            {
                var elem = $(id);
                elem.disabled = true;
                elem.addClassName("disabled");
            }


            $("cosmocommerce_updater").select("input").each(function(elem) {
                Event.observe($(elem.id), "change", disableGenerateButton(elem.id));
            });
            //]]>
            </script>';
            
            
            foreach(glob($modman_path."/*",GLOB_ONLYDIR) as $_subfolder){
            
                $repo = new Git2\Repository($_subfolder);
               
                $foldername= basename($_subfolder);
                //print_r($repo);
                $ref = Git2\Reference::lookup($repo, "refs/heads/master");
                //print_r(get_class_methods(new Git2\Repository($_subfolder)));
                //print_r(get_class_vars('Git2\Repository'));
                //print_r(get_object_vars($repo));
                //print_r($ref);
                //echo $ref->getName() . PHP_EOL;
                $version=$ref->getTarget();
                
                
                //$remoteref = Git2\Reference::lookup($repo, "refs/remotes/origin/master");
                //print_r($repo->lookup());
                //ini_set('display_errors',1);
                
                //以后考虑要做一个模块，定时把github版本记录下来。不用经常远程查询
                
                $ch =  curl_init("https://api.github.com/repos/cosmocommerce/".$foldername."/commits");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $content = json_decode(curl_exec($ch));
                
                //$content=json_decode(file_get_contents("https://api.github.com/repos/cosmocommerce/".$foldername."/commits"));
                $remoteversion=$content[0]->sha;
                //$remoteversion='af';
                
                
                
                $config = new Git2\Config($_subfolder."/.git/config");
                $remoteurl=( $config->get("remote")['origin']['url'] );

                $path = $_subfolder;
                $output="";
                $class="";
                chdir($path);
                $last_line = exec(escapeshellcmd('git status'), $output,$retval);
                if($last_line=="nothing to commit (working directory clean)"){
                    $class="success";
                }else{
                    $class="fail";
                }
                if($last_line=='no changes added to commit (use "git add" and/or "git commit -a")'){
                    $class="fail";
                }
                $html .= '<li>';
                $html .= '<button type="button" class="scalable '.$class.'"  style=""><span><span><span>'.$foldername.'</span></span></span></button> ';
                $html .= '<div><br /><button id="updateBtn'.$_htmlId.$foldername.'" value="'.$foldername.'" type="button" class="scalable save" onclick="" style=""><span><span><span>更新</span></span></span></button> ';
                $html .= '<button id="commitBtn'.$_htmlId.$foldername.'" value="'.$foldername.'" type="button" class="scalable save" onclick="" style=""><span><span><span>提交</span></span></span></button> </div>';
                
                $html .= '<div style="float:right;"><button id="updatefBtn'.$_htmlId.$foldername.'" value="'.$foldername.'" type="button" class="scalable save" onclick="" style=""><span><span><span>强制更新</span></span></span></button> </div>';
                $html .= '
                <script type="text/javascript">
                //<![CDATA[
                Event.observe("updateBtn'.$_htmlId.$foldername.'", "click", redirectToUpdate);
                Event.observe("updatefBtn'.$_htmlId.$foldername.'", "click", redirectToUpdatef);
                Event.observe("commitBtn'.$_htmlId.$foldername.'", "click", redirectToCommit);
                //]]>
                </script>';
                
                if($version==$remoteversion){
                    $html .= '版本一致:<br />'.$version."<br />";
                }else{
                    $html .= '本地版本:<br />'.$version."<br />";
                    $html .= '远程版本:<br />'.$remoteversion."<br />";
                }
                    if($class=='fail'){
                        $html .= '修改说明:<br />';
                        $html .= implode('<br />',$output)."<br />";
                    }
                //$html .= $retval."<br />";
                //$html .= "<b>".$last_line."</b><br />";
                $html .= '</li>';
            
            }
            $html.="</ul>";
            
            
            
                   
        
        
         }
        
        
        
        
        
        return $html;
    }
}