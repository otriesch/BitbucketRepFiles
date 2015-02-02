<?php
    /**
     * Plugin: BitbucketRepFiles
     * 
     * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
     * @author Olaf Trieschmann <develop@otri.de> 
     * 
     * Thanks to Igor Kromin's (http://www.igorkromin.net) plugin "GitLab Commits", which was used as a starting point!
     */
    
    if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
    if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
    require_once(DOKU_PLUGIN.’syntax.php');
    
    class syntax_plugin_bitbucketrepfiles extends DokuWiki_Syntax_Plugin {
        
        function getInfo() {
            return array('author' => 'Olaf Trieschmann',
                         'email'  => 'develop@otri.de',
                         'date'   => '2015-02-01′,
                         'name'   => 'BitbucketRepFiles Plugin',
                         'desc'   => 'Dokuwiki plugin to show the files in a Bitbucket repository',
                         'url'    => 'https://github.com/otriesch/itemtable/raw/master/bitbucketrepfiles.zip');
        }
        
        function getType(){ return ’substition'; }
        function getPType(){ return 'block'; }
        function getSort(){ return 100; }
        
        function connectTo($mode) {
            $this->Lexer->addSpecialPattern('[bitbucketrepfiles]',$mode,'plugin_bitbucketrepfiles');
        }
        
        function handle($match, $state, $pos, &$handler){
            return array($match, $state, $pos);
        }
        
        function render($mode, &$renderer, $data) {
            if($mode == 'xhtml'){
                $pageurl = "https://bitbucket.org/api/1.0/repositories/otrima/randt-ansible/branches/";
                $ch=curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $pageurl);
                $html = curl_exec($ch);
                curl_close($ch);
                
                $json = new JSON();
                $array  = $json->decode($html);
                
                $renderer->doc .= '<table class="pagelist" style="width:725px"><tr><th class="page">ID</th><th class="page">Author</th><th class="page">Title</th><th class="date">Date</th></tr>';
                foreach($array as &$val) {
                    $renderer->doc .= '<tr><td class="desc">' . $val->short_id . '</td><td class="desc">' . $val->author_name . '</td><td class="desc">' . $val->title . '</td><td class="date">' . $val->created_at . '</td></tr>';
                }
                $renderer->doc .= '</table>';
                
                return true;
            }
            return false;
        }
    }
    
?>
