<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Image\Rendition;

/**
 * @Acl(ignore=True)
 */
class Admin_ImageController extends Zend_Controller_Action
{
    protected $renditions = array();

    public function init()
    {
        $this->renditions = array(
            'thumbnail' => new Rendition(75, 75, 'fill_crop', 'thumbnail'),
            'square' => new Rendition(150, 150, 'fill_crop', 'square'),
            'landscape' => new Rendition(400, 300, 'fill_crop', 'landscape'),
            'portrait' => new Rendition(300, 400, 'fill_crop', 'portrait'),
        );
    }

    public function articleAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $this->view->renditions = $this->renditions;
        $this->view->images = $this->_helper->service('image')->findByArticle($this->_getParam('article_number'));
        $this->view->articleRenditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
    }

    public function setRenditionAction()
    {
        $this->_helper->layout->disableLayout();
        $rendition = $this->renditions[array_shift(explode(' ', $this->_getParam('rendition')))];
        $image = $this->_helper->service('image')->getArticleImage($this->_getParam('article_number'), array_pop(explode('-', $this->_getParam('image'))));
        $this->view->imageRendition = $this->_helper->service('image.rendition')->setRenditionImage($rendition, $image);
        $this->view->rendition = $rendition;
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $rendition = $this->renditions[$this->_getParam('rendition')];
        $renditions = $this->_helper->service('image.rendition')->getArticleRenditions($this->_getParam('article_number'));
        $this->view->rendition = $rendition;
        $this->view->image = $renditions[$rendition]->getImage();
    }
}