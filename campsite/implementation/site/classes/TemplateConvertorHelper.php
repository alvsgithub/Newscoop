<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/TemplateConvertorListObject.php');

define('CS_OBJECT', '$campsite');


/**
 * Class TemplateConvertorHelper
 */
class TemplateConvertorHelper
{
    /**
     * @var array
     */
    private static $m_exceptions = array(
        'article' => array(
            'print' => array(
                'type' => array(
                    'attribute' => 'type_name'),
                'mon_nr' => array(
                    'attribute' => 'mon'),
                'wday_nr' => array(
                    'attribute' => 'wday'),
                'upload_date' => array(
                    'attribute' => 'creation_date'),
                'uploaddate' => array(
                    'attribute' => 'creation_date'),
                'publishdate' => array(
                    'attribute' => 'publish_date'),
                'onfrontpage' => array(
                    'attribute' => 'on_front_page'),
                'onsectionpage' => array(
                    'attribute' => 'on_section_page'),
                'public' => array(
                    'attribute' => 'is_public')
                )
            ),
        'articlecomment' => array(
            'print' => array(
                'readeremail' => array(
                    'new_object' => 'comment',
                    'attribute' => 'reader_email'),
                'readeremailobfuscated' => array(
                    'new_object' => 'comment',
                    'attribute' => 'reader_email|obfuscate_email'),
                'submitdate' => array(
                    'new_object' => 'comment',
                    'attribute' => 'submit_date'),
                'readeremailpreview' => array(
                    'new_object' => 'preview_comment_action',
                    'attribute' => 'reader_email'),
                'readeremailpreviewobfuscated' => array(
                    'new_object' => 'preview_comment_action',
                    'attribute' => 'reader_email|obfuscate_email'),
                'subjectpreview' => array(
                    'new_object' => 'preview_comment_action',
                    'attribute' => 'subject'),
                'contentpreview' => array(
                    'new_object' => 'preview_comment_action',
                    'attribute' => 'content'),
                'submiterror' => array(
                    'new_object' => 'submit_comment_action',
                    'attribute' => 'error_message'),
                'submiterrorno' => array(
                    'new_object' => 'submit_comment_action',
                    'attribute' => 'error_code')
                )
            ),
        'attachment' => array(
            'print' => array(
                'filename' => array(
                    'attribute' => 'file_name'),
                'mimetype' => array(
                    'attribute' => 'mime_type'),
                'sizeb' => array(
                    'attribute' => 'size_b'),
                'sizekb' => array(
                    'attribute' => 'size_kb'),
                'sizemb' => array(
                    'attribute' => 'size_mb')
                )
            ),
        'audioattachment' => array(
            'print' => array(
                'tracknum' => array(
                    'attribute' => 'track_no'),
                'disknum' => array(
                    'attribute' => 'disk_no')
                )
            ),
        'image' => array(
            'print' => array(
                'mon_nr' => array(
                    'attribute' => 'mon'),
                'wday_nr' => array(
                    'attribute' => 'wday')
                )
            ),
        'issue' => array(
            'print' => array(
                'mon_nr' => array(
                    'attribute' => 'mon'),
                'wday_nr' => array(
                    'attribute' => 'wday'),
                'iscurrent' => array(
                    'attribute' => 'is_current')
                )
            ),
        'language' => array(
            'print' => array(
                'englname' => array (
                    'attribute' => 'english_name')
                )
            ),
        'subscription' => array(
            'print' => array(
                'expdate' => array(
                    'attribute' => 'expiration_date')
                )
            ),
        'user' => array(
            'print' => array(
                'straddress' => array (
                    'attribute' => 'str_address'),
                'postalcode' => array(
                    'attribute' => 'postal_code'),
                'addok' => array(
                    'attribute' => 'add_ok'),
                'modifyok' => array(
                    'attribute' => 'modify_ok'),
                'adderror' => array(
                    'attribute' => 'add_error'),
                'modifyerror' => array(
                    'attribute' => 'modify_error'),
                'addaction' => array(
                    'attribute' => 'add_action'),
                'loggedin' => array(
                    'attribute' => 'logged_in'),
                'blockedfromcomments' => array(
                    'attribute' => 'blocked_from_comments')
                )
            )
        );


    /**
     * @var array
     */
    private static $m_envObjects = array('language','publication','issue',
                                         'section','article','topic', 'articlecomment');

    /**
     * @var array
     */
    private static $m_printEx = array('articleattachment' => 'attachment',
                                      'articlecomment' => 'comment');

    /**
     * @var array
     */
    private static $m_urXFuncs = array('uri','uripath','url','urlparameters');

    /**
     * @var array
     */
    private static $m_simpleForms = array('login','search','user');

    /**
     * @var array
     */
    private static $m_endForms = array('endarticlecomment','endlogin','endsearch',
                                       'endsubscription','enduser');


    /**
     * @param array $p_optArray
     */
    public static function GetNewTagContent($p_optArray)
    {
        //static $listObj;

        if (!is_array($p_optArray) || sizeof($p_optArray) < 1) {
            continue;
        }

        $newTag = '';
        $p_optArray[0] = strtolower($p_optArray[0]);

        // <!** Print statement ... > to {{ $campsite->statement ... }}
        if ($p_optArray[0] == 'print') {
            return self::BuildPrintStatement($p_optArray);
        }

        // Environmental Objects
        // <!** Publication name ... > to {{ set_publication name= ... }}
        // <!** Issue off ... > to {{ unset_issue }}
        if (in_array($p_optArray[0], self::$m_envObjects)) {
            return self::BuildEnvironmentalStatement($p_optArray);
        }

        // URI and URL Functions
        // <!** URI> to {{ uri }}
        // <!** URLParameters section> to {{ urlparameters options="section" }}
        if (in_array($p_optArray[0], self::$m_urXFuncs)) {
            return self::BuildUrxStatement($p_optArray);
        }

        // User, Search and Login Forms
        // <!** Search do-search.tpl Go>
        // to
        // {{ search_form template="do-search.tpl" submit_button="Go" }}
        if (in_array($p_optArray[0], self::$m_simpleForms)) {
            return self::BuildSimpleFormStatement($p_optArray);
        }

        // Article Comment Form
        // <!** ArticleCommentForm ArtCommForm.tpl Send Preview>
        // to
        // {{ article_comment_form template="ArtCommForm.tpl"
        //    submit_button="Send" preview_button="Preview" }}
        if ($p_optArray[0] == 'articlecommentform') {
            return self::BuildArticleCommentFormStatement($p_optArray);
        }

        // Subscription Comment Form
        // <!** Subscription by_section do-subsc.tpl Subscribe>
        // to
        // {{ subscription_form type="by_section" template="do-subsc.tpl"
        //    submit_button="Subscribe" }}
        if ($p_optArray[0] == 'subscription') {
            return self::BuildSubscriptionFormStatement($p_optArray);
        }

        // End forms
        // <!** EndSearch> to {{ /search_form }}
        if (in_array($p_optArray[0], self::$m_endForms)) {
            return self::BuildEndFormStatement($p_optArray);
        }

        // Edit form fields
        // <!** Edit Login uname>
        // <!** Edit Search keywords>
        if ($p_optArray[0] == 'edit') {
            return self::BuildEditStatement($p_optArray);
        }

        // Select form fields
        // <!** Select Login RememberLogin>
        // to
        // {{ camp_select object="login" attribute="rememberlogin" }}
        //
        // <!** Select User gender male female>
        // to
        // {{ camp_select object="user" attribute="gender" male_name="M" female_name="M" }}
        if ($p_optArray[0] == 'select') {
            return self::BuildSelectStatement($p_optArray);
        }

        // HTML Encoding
        // <!** HTMLEncoding> to {{ enable_html_encoding }}
        // <!** HTMLEncoding off> to {{ disable_html_encoding }}
        if ($p_optArray[0] == 'htmlencoding') {
            return self::BuildHTMLEncodingStatement($p_optArray);
        }

        switch ($p_optArray[0]) {
        // <!** Date ... > to {{ $smarty.now|camp_date_format:" ... " }}
        case 'date':
            $newTag = '$smarty.now|camp_date_format:"' . $p_optArray[1] . '"';
            break;
        // <!** include header.tpl> to {{ include file="header.tpl" }}
        case 'include':
            $newTag = 'include file="' . $p_optArray[1] . '"';
            break;
        // <!** local> to {{ local }}
        case 'local':
            $newTag = 'local';
            break;
        // <!** endLocal> to {{ /local }}
        case 'endlocal':
            $newTag = '/local';
            break;
        // <!** endIf> to {{ /if }}
        case 'endif':
            $newTag = '/if';
            break;
        // <!** else> to {{ /else }}
        case 'else':
            $newTag = '/else';
            break;
        }
        
        return $newTag;
    } // fn GetNewTagContent


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildPrintStatement($p_optArray)
    {
        $newTag = CS_OBJECT;
        $exStatement = strtolower($p_optArray[1]);
        if (array_key_exists($exStatement, self::$m_exceptions)
                && array_key_exists(strtolower($p_optArray[2]), self::$m_exceptions[$exStatement]['print'])) {
            $e = self::$m_exceptions[$exStatement]['print'][strtolower($p_optArray[2])];
            $newTag .= (isset($e['new_object'])) ? '->'.$e['new_object'] : '->'.strtolower($p_optArray[1]);
            $newTag .= (isset($e['attribute'])) ? '->'.$e['attribute'] : '';
        } else {
            for($i = 1; $i < sizeof($p_optArray); $i++) {
                if (array_key_exists(strtolower($p_optArray[$i]), self::$m_printEx)) {
                    $p_optArray[$i] = self::$m_printEx[strtolower($p_optArray[$i])];
                }
                $newTag .= '->' . strtolower($p_optArray[$i]);
            }
        }
        
        return $newTag;    
    } // fn BuildPrintStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildEnvironmentalStatement($p_optArray)
    {
        if (strtolower($p_optArray[1]) == 'off') {
            if ($p_optArray[0] == 'articlecomment') {
                $newTag = 'unset_article_comment';
            } else {
                $newTag = 'unset_' . $p_optArray[0];
            }
        } else {
            $newTag = 'set_' . $p_optArray[0];
                if ($p_optArray[0] == 'language') {
                $newTag.= ' name="' . strtolower($p_optArray[1]) . '"';
            } else {
                $newTag.= (isset($p_optArray[1])) ? ' ' . strtolower($p_optArray[1]) : '';
                $newTag.= (isset($p_optArray[2])) ? '="' . strtolower($p_optArray[2]) . '"' : '';
            }
        }

        return $newTag;
    } // fn BuildEnvironmentalStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildUrxStatement($p_optArray)
    {
        $newTag = $p_optArray[0];
        if (sizeof($p_optArray) > 1) {
            $newTag.= ' options="';
            for ($x = 1; $x < sizeof($p_optArray); $x++) {
                $newTag.= ($x > 1) ? ' ' : '';
                $newTag.= strtolower($p_optArray[$x]);
            }
            $newTag.= '"';
        }

        return $newTag;
    } // fn BuildUrxStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildSimpleFormStatement($p_optArray)
    {
        $newTag = $p_optArray[0] . '_form';
        $newTag.= (isset($p_optArray[1])) ? ' template="' . $p_optArray[1] . '"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' submit_button="' . $p_optArray[2] . '"' : '';
        $newTag.= (isset($p_optArray[3])) ? ' html_code="' . $p_optArray[3] . '"' : '';

        return $newTag;
    } // fn BuildSimpleFormStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildArticleCommentFormStatement($p_optArray)
    {
        $newTag = 'article_comment_form';
        $newTag.= (isset($p_optArray[1])) ? ' template="'.$p_optArray[1].'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' submit_button="'.$p_optArray[2].'"' : '';
        $newTag.= (isset($p_optArray[3])) ? ' preview_button="'.$p_optArray[3].'"' : '';

        return $newTag;
    } // fn BuildArticleCommentFormStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildSubscriptionFormStatement($p_optArray)
    {
        $newTag = 'subscription_form';
        $newTag.= (isset($p_optArray[1])) ? ' type="'.$p_optArray[1].'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' template="'.$p_optArray[2].'"' : '';
        $newTag.= (isset($p_optArray[3])) ? ' submit_button="'.$p_optArray[3].'"' : '';
        $newTag.= (isset($p_optArray[4])) ? ' total_field="'.$p_optArray[4].'"' : '';
        $newTag.= (isset($p_optArray[5])) ? ' evaluated_field="'.$p_optArray[5].'"' : '';

        return $newTag;
    } // fn BuildArticleCommentFormStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildEndFormStatement($p_optArray)
    {
        $newTag = '/' . substr($p_optArray[0], 3) . '_form';
        return $newTag;
    } // fn BuildEndFormStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildEditStatement($p_optArray)
    {
        $newTag = 'camp_edit';
        $newTag.= (isset($p_optArray[1])) ? ' object="'.strtolower($p_optArray[1]).'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' attribute="'.strtolower($p_optArray[2]).'"' : '';
        if (isset($p_optArray[3])) {
            if (is_integer($p_optArray[3])) {
                $newTag.= ' size="'.$p_optArray[3].'"';
            } elseif (strtolower($p_optArray[3]) == 'html') {
                $newTag.= ' html_code="';
                $newTag.= (isset($p_optArray[4])) ? $p_optArray[4].'"' : '"';
            }
        }

        return $newTag;
    } // fn BuildEditStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildSelectStatement($p_optArray)
    {
        $newTag = 'camp_select';
        $newTag.= (isset($p_optArray[1])) ? ' object="'.strtolower($p_optArray[1]).'"' : '';
        $newTag.= (isset($p_optArray[2])) ? ' attribute="'.strtolower($p_optArray[2]).'"' : '';
        if (strtolower($p_optArray[2]) == 'gender') {
            $newTag.= (isset($p_optArray[3])) ? ' male_name="'.strtolower($p_optArray[3]).'"' : '';
            $newTag.= (isset($p_optArray[4])) ? ' female_name="'.strtolower($p_optArray[4]).'"' : '';
        }

        return $newTag;
    } // fn BuildSelectStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildListStatement($p_optArray, &$listObj)
    {
        $listObj = new TemplateConvertorListObject($p_optArray);
        $newTag = $listObj->getListString();

        return $newTag;
    } // fn BuildListStatement


    /**
     * @param array $p_optArray
     *
     * @return string $newTag
     */
    public static function BuildHTMLEncodingStatement($p_optArray)
    {
        if (isset($p_optArray[1]) && strtolower($p_optArray[1]) == 'off') {
            $newTag = 'disable_html_encoding';
        } else {
            $newTag = 'enable_html_encoding';
        }

        return $newTag;
    } // fn BuildHTMLEncodingStatement

} // class TemplateConvertorHelper

?>