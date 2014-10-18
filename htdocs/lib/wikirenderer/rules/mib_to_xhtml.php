<?php
/**
 * wikirenderer3 (wr3) syntax to xhtml
 *
 * @package WikiRenderer
 * @subpackage rules
 * @author Laurent Jouanneau
 * @copyright 2003-2006 Laurent Jouanneau
 * @link http://wikirenderer.jelix.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public 2.1
 * License as published by the Free Software Foundation.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */
require_once 'wr3_to_xhtml.php';

class mib_to_xhtml  extends wr3_to_xhtml  {

   public $textLineContainers = array('WikiHtmlTextLine'=> array( 'wr3xhtml_strong','wr3xhtml_em','wr3xhtml_code','wr3xhtml_q',
    'wr3xhtml_cite','wr3xhtml_acronym','mibxhtml_link', 'wr3xhtml_image',
    'wr3xhtml_anchor', 'wr3xhtml_footnote'));
}

// ===================================== déclarations des tags inlines

class mibxhtml_link extends WikiTagXhtml {
    protected $name='a';
    public $beginTag='[[';
    public $endTag=']]';
    protected $attribute=array('$$','href','hreflang','title');
    public $separators=array('|');
    public function getContent(){
        $cntattr=count($this->attribute);
        $cnt=($this->separatorCount + 1 > $cntattr?$cntattr:$this->separatorCount+1);
        if($cnt == 1 ){
            list($href, $label) = $this->config->processLink($this->wikiContentArr[0], $this->name);
            return '<a href="'.BASE_URL.'post.php?f='.htmlspecialchars($href).'">'.htmlspecialchars($label).'</a>';
        }else{
            list($href, $label) = $this->config->processLink($this->wikiContentArr[1], $this->name);
            $this->wikiContentArr[1] = $href;
            return parent::getContent();
        }
    }
}
