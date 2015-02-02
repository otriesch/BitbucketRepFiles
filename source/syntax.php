<?php
/**
 * Plugin: BitbucketRepFiles
 * 
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Olaf Trieschmann <develop@otri.de> 
 * 
 * Thanks to Igor Kromin's plugin "GitLab Commits"
 * (http://www.igorkromin.net/index.php/2013/04/27/adding-gitlab-commits-to-a-dokuwiki-page-a-very-rough-plugin/), 
 * which was used as a starting point!
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
    
class syntax_plugin_bitbucketrepfiles extends DokuWiki_Syntax_Plugin {
    
    function getInfo() {
        return array('author' => 'Olaf Trieschmann',
                     'email'  => 'develop@otri.de',
                     'date'   => '2015-02-01',
                     'name'   => 'BitbucketRepFiles Plugin',
                     'desc'   => 'Dokuwiki plugin to show the files in a Bitbucket repository',
                     'url'    => 'https://github.com/otriesch/itemtable/raw/master/bitbucketrepfiles.zip');
    }
    
    function getType(){ return 'substition'; }
    function getPType(){ return 'block'; }
    function getSort(){ return 100; }
    
    /**
     * Connect pattern to lexer
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{bitbucketrepfiles>.+?\}\}',$mode,'plugin_bitbucketrepfiles');
    }
    
    public function handle($match, $state, $pos, Doku_Renderer $handler){
	$start = strlen('{{bitbucketrepfiles>');
	$end = -2;
	
	$params = substr($match, $start, $end);
	$params = preg_replace('/\s{2,}/', '', $params);
	$params = preg_replace('/\s[=]/', '=', $params);
	$params = preg_replace('/[=]\s/', '=', $params);
	
	$data = array();
	foreach(explode('&', $params) as $param)
	{
		$val = explode('=', $param);
		$data[$val[0]] = $val[1];
	}
       return $return;

//        return array($data, $state, $pos);
    }
    
    /**
     * Handles the actual output creation.
     *
     * @param   $mode       string        output format being rendered
     * @param   $renderer   Doku_Renderer the current renderer object
     * @param   $data       array         data created by handler()
     * @return  boolean                   rendered correctly?
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode == 'xhtml') && isset($data['URL']){
//            $pageurl = $data['URL']
            $pageurl = "https://bitbucket.org/api/1.0/repositories/otrima/randt-ansible/branches/";
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $pageurl);
            $html = curl_exec($ch);
            curl_close($ch);
            
            $json = new JSON();
            $array = $json->decode($html);
            $array = $val->master;
            $array = $val->files;
            
            $renderer->doc .= '<table class="pagelist" style="width:725px"><tr><th class="page">File</th></tr>';
            foreach($array as &$val) {
                $renderer->doc .= '<tr><td class="desc">' . $val->file '</tr>';
            }
            $renderer->doc .= '</table>';
            
            return true;
        }
        return false;
    }
}
    
?>
