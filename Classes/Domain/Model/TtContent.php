<?php

namespace Extcode\Cart\Domain\Model;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * TtContent Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class TtContent extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $headerPosition;

    /**
     * @var string
     */
    protected $bodytext;

    /**
     * @var integer
     */
    protected $colPos;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var integer
     */
    protected $imagewidth;

    /**
     * @var integer
     */
    protected $imageheight;

    /**
     * @var integer
     */
    protected $imageorient;

    /**
     * @var string
     */
    protected $imagecaption;

    /**
     * @var integer
     */
    protected $imagecols;

    /**
     * @var integer
     */
    protected $imageborder;

    /**
     * @var string
     */
    protected $media;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var integer
     */
    protected $cols;

    /**
     * @var string
     */
    protected $subheader;

    /**
     * @var string
     */
    protected $headerLink;

    /**
     * @var string
     */
    protected $imageLink;

    /**
     * @var string
     */
    protected $imageZoom;

    /**
     * @var string
     */
    protected $altText;

    /**
     * @var string
     */
    protected $titleText;

    /**
     * @var string
     */
    protected $headerLayout;

    /**
     * @var string
     */
    protected $listType;

    /**
     * @var string
     */
    protected $records;

    /**
     * @var string
     */
    protected $pages;

    /**
     * @var string
     */
    protected $feGroup;

    /**
     * @var string
     */
    protected $imagecaptionPosition;

    /**
     * @var string
     */
    protected $longdescUrl;

    /**
     * @var string
     */
    protected $menuType;

    /**
     * @var string
     */
    protected $selectKey;

    /**
     * @var string
     */
    protected $fileCollections;

    /**
     * @var string
     */
    protected $filelinkSorting;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var string
     */
    protected $multimedia;

    /**
     * @var string
     */
    protected $piFlexform;

    /**
     * @var string
     */
    protected $accessibilityTitle;

    /**
     * @var string
     */
    protected $accessibilityBypassText;

    /**
     * @var string
     */
    protected $selectedCategories;

    /**
     * @var string
     */
    protected $categoryField;

    /**
     * @var integer
     */
    protected $spaceBefore;

    /**
     * @var integer
     */
    protected $spaceAfter;

    /**
     * @var integer
     */
    protected $imageNoRows;

    /**
     * @var integer
     */
    protected $imageEffects;

    /**
     * @var integer
     */
    protected $imageCompression;

    /**
     * @var integer
     */
    protected $tableBorder;

    /**
     * @var integer
     */
    protected $tableCellspacing;

    /**
     * @var integer
     */
    protected $tableCellpadding;

    /**
     * @var integer
     */
    protected $tableBgColor;

    /**
     * @var integer
     */
    protected $sectionIndex;

    /**
     * @var integer
     */
    protected $linkToTop;

    /**
     * @var integer
     */
    protected $filelinkSize;

    /**
     * @var integer
     */
    protected $sectionFrame;

    /**
     * @var integer
     */
    protected $date;

    /**
     * @var integer
     */
    protected $imageFrames;

    /**
     * @var integer
     */
    protected $recursive;

    /**
     * @var integer
     */
    protected $rteEnabled;

    /**
     * @var integer
     */
    protected $txImpexpOriguid;

    /**
     * @var integer
     */
    protected $accessibilityBypass;

    /**
     * @var integer
     */
    protected $sysLanguageUid;

    /**
     * @var integer
     */
    protected $starttime;

    /**
     * @var integer
     */
    protected $endtime;

    /**
     * @var string
     */
    protected $txGridelementsBackendLayout;

    /**
     * @var integer
     */
    protected $txGridelementsChildren;

    /**
     * @var integer
     */
    protected $txGridelementsContainer;

    /**
     * @var integer
     */
    protected $txGridelementsColumns;

    /**
     * @return DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * @param $crdate
     * @return void
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * @return DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * @param $tstamp
     * @return void
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param $contentType
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param $header
     * @return void
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getHeaderPosition()
    {
        return $this->headerPosition;
    }

    /**
     * @param $headerPosition
     * @return void
     */
    public function setHeaderPosition($headerPosition)
    {
        $this->headerPosition = $headerPosition;
    }

    /**
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * @param $bodytext
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get the colpos
     *
     * @return integer
     */
    public function getColPos()
    {
        return (int)$this->colPos;
    }

    /**
     * Set colpos
     *
     * @param integer $colPos
     * @return void
     */
    public function setColPos($colPos)
    {
        $this->colPos = $colPos;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param $image
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getImagewidth()
    {
        return $this->imagewidth;
    }

    /**
     * @param $imagewidth
     * @return void
     */
    public function setImagewidth($imagewidth)
    {
        $this->imagewidth = $imagewidth;
    }

    /**
     * @return int
     */
    public function getImageheight()
    {
        return $this->imageheight;
    }

    /**
     * @param $imageheight
     * @return void
     */
    public function setImageheight($imageheight)
    {
        $this->imageheight = $imageheight;
    }

    /**
     * @return int
     */
    public function getImageorient()
    {
        return $this->imageorient;
    }

    /**
     * @param $imageorient
     * @return void
     */
    public function setImageorient($imageorient)
    {
        $this->imageorient = $imageorient;
    }

    /**
     * @return string
     */
    public function getImagecaption()
    {
        return $this->imagecaption;
    }

    /**
     * @param $imagecaption
     * @return void
     */
    public function setImagecaption($imagecaption)
    {
        $this->imagecaption = $imagecaption;
    }

    /**
     * @return int
     */
    public function getImagecols()
    {
        return $this->imagecols;
    }

    /**
     * @param $imagecols
     * @return void
     */
    public function setImagecols($imagecols)
    {
        $this->imagecols = $imagecols;
    }

    /**
     * @return int
     */
    public function getImageborder()
    {
        return $this->imageborder;
    }

    /**
     * @param $imageborder
     * @return void
     */
    public function setImageborder($imageborder)
    {
        $this->imageborder = $imageborder;
    }

    /**
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param $media
     * @return void
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param $cols
     * @return void
     */
    public function setCols($cols)
    {
        $this->cols = $cols;
    }

    /**
     * @return string
     */
    public function getSubheader()
    {
        return $this->subheader;
    }

    /**
     * @param $subheader
     * @return void
     */
    public function setSubheader($subheader)
    {
        $this->subheader = $subheader;
    }

    /**
     * @return string
     */
    public function getHeaderLink()
    {
        return $this->headerLink;
    }

    /**
     * @param $headerLink
     * @return void
     */
    public function setHeaderLink($headerLink)
    {
        $this->headerLink = $headerLink;
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param $imageLink
     * @return void
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
    }

    /**
     * @return string
     */
    public function getImageZoom()
    {
        return $this->imageZoom;
    }

    /**
     * @param $imageZoom
     * @return void
     */
    public function setImageZoom($imageZoom)
    {
        $this->imageZoom = $imageZoom;
    }

    /**
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
    }

    /**
     * @param $altText
     * @return void
     */
    public function setAltText($altText)
    {
        $this->altText = $altText;
    }

    /**
     * @return string
     */
    public function getTitleText()
    {
        return $this->titleText;
    }

    /**
     * @param $titleText
     * @return void
     */
    public function setTitleText($titleText)
    {
        $this->titleText = $titleText;
    }

    /**
     * @return string
     */
    public function getHeaderLayout()
    {
        return $this->headerLayout;
    }

    /**
     * @param $headerLayout
     * @return void
     */
    public function setHeaderLayout($headerLayout)
    {
        $this->headerLayout = $headerLayout;
    }

    /**
     * @return string
     */
    public function getListType()
    {
        return $this->listType;
    }

    /**
     * @param string $listType
     * @return void
     */
    public function setListType($listType)
    {
        $this->listType = $listType;
    }

    /**
     * @return string
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param $records
     * @return void
     */
    public function setRecords($records)
    {
        $this->records = $records;
    }

    /**
     * @return string
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param $pages
     * @return void
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @return string
     */
    public function getFeGroup()
    {
        return $this->feGroup;
    }

    /**
     * @param $feGroup
     * @return void
     */
    public function setFeGroup($feGroup)
    {
        $this->feGroup = $feGroup;
    }

    /**
     * @return string
     */
    public function getImagecaptionPosition()
    {
        return $this->imagecaptionPosition;
    }

    /**
     * @param $imagecaptionPosition
     * @return void
     */
    public function setImagecaptionPosition($imagecaptionPosition)
    {
        $this->imagecaptionPosition = $imagecaptionPosition;
    }

    /**
     * @return string
     */
    public function getLongdescUrl()
    {
        return $this->longdescUrl;
    }

    /**
     * @param $longdescUrl
     * @return void
     */
    public function setLongdescUrl($longdescUrl)
    {
        $this->longdescUrl = $longdescUrl;
    }

    /**
     * @return string
     */
    public function getMenuType()
    {
        return $this->menuType;
    }

    /**
     * @param $menuType
     * @return void
     */
    public function setMenuType($menuType)
    {
        $this->menuType = $menuType;
    }

    /**
     * @return string
     */
    public function getSelectKey()
    {
        return $this->selectKey;
    }

    /**
     * @param $selectKey
     * @return void
     */
    public function setSelectKey($selectKey)
    {
        $this->selectKey = $selectKey;
    }

    /**
     * @return string
     */
    public function getFileCollections()
    {
        return $this->fileCollections;
    }

    /**
     * @param $fileCollections
     * @return void
     */
    public function setFileCollections($fileCollections)
    {
        $this->fileCollections = $fileCollections;
    }

    /**
     * @return string
     */
    public function getFilelinkSorting()
    {
        return $this->filelinkSorting;
    }

    /**
     * @param $filelinkSorting
     * @return void
     */
    public function setFilelinkSorting($filelinkSorting)
    {
        $this->filelinkSorting = $filelinkSorting;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $target
     * @return void
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getMultimedia()
    {
        return $this->multimedia;
    }

    /**
     * @param $multimedia
     * @return void
     */
    public function setMultimedia($multimedia)
    {
        $this->multimedia = $multimedia;
    }

    /**
     * @return string
     */
    public function getPiFlexform()
    {
        return $this->piFlexform;
    }

    /**
     * @param $piFlexform
     * @return void
     */
    public function setPiFlexform($piFlexform)
    {
        $this->piFlexform = $piFlexform;
    }

    /**
     * @return string
     */
    public function getAccessibilityTitle()
    {
        return $this->accessibilityTitle;
    }

    /**
     * @param $accessibilityTitle
     * @return void
     */
    public function setAccessibilityTitle($accessibilityTitle)
    {
        $this->accessibilityTitle = $accessibilityTitle;
    }

    /**
     * @return string
     */
    public function getAccessibilityBypassText()
    {
        return $this->accessibilityBypassText;
    }

    /**
     * @param $accessibilityBypassText
     * @return void
     */
    public function setAccessibilityBypassText($accessibilityBypassText)
    {
        $this->accessibilityBypassText = $accessibilityBypassText;
    }

    /**string
     * @return string
     */
    public function getSelectedCategories()
    {
        return $this->selectedCategories;
    }

    /**
     * @param $selectedCategories
     * @return void
     */
    public function setSelectedCategories($selectedCategories)
    {
        $this->selectedCategories = $selectedCategories;
    }

    /**
     * @return string
     */
    public function getCategoryField()
    {
        return $this->categoryField;
    }

    /**
     * @param $categoryField
     * @return void
     */
    public function setCategoryField($categoryField)
    {
        $this->categoryField = $categoryField;
    }

    /**
     * @return integer
     */
    public function getSpaceBefore()
    {
        return $this->spaceBefore;
    }

    /**
     * @param $spaceBefore
     * @return void
     */
    public function setSpaceBefore($spaceBefore)
    {
        $this->spaceBefore = $spaceBefore;
    }

    /**
     * @return integer
     */
    public function getSpaceAfter()
    {
        return $this->spaceAfter;
    }

    /**
     * @param $spaceAfter
     * @return void
     */
    public function setSpaceAfter($spaceAfter)
    {
        $this->spaceAfter = $spaceAfter;
    }

    /**
     * @return integer
     */
    public function getImageNoRows()
    {
        return $this->imageNoRows;
    }

    /**
     * @param $imageNoRows
     * @return void
     */
    public function setImageNoRows($imageNoRows)
    {
        $this->imageNoRows = $imageNoRows;
    }

    /**
     * @return integer
     */
    public function getImageEffects()
    {
        return $this->imageEffects;
    }

    /**
     * @param $imageEffects
     * @return void
     */
    public function setImageEffects($imageEffects)
    {
        $this->imageEffects = $imageEffects;
    }

    /**
     * @return integer
     */
    public function getImageCompression()
    {
        return $this->imageCompression;
    }

    /**
     * @param $imageCompression
     * @return void
     */
    public function setImageCompression($imageCompression)
    {
        $this->imageCompression = $imageCompression;
    }

    /**
     * @return integer
     */
    public function getTableBorder()
    {
        return $this->tableBorder;
    }

    /**
     * @param $tableBorder
     * @return void
     */
    public function setTableBorder($tableBorder)
    {
        $this->tableBorder = $tableBorder;
    }

    /**
     * @return integer
     */
    public function getTableCellspacing()
    {
        return $this->tableCellspacing;
    }

    /**
     * @param $tableCellspacing
     * @return void
     */
    public function setTableCellspacing($tableCellspacing)
    {
        $this->tableCellspacing = $tableCellspacing;
    }

    /**
     * @return integer
     */
    public function getTableCellpadding()
    {
        return $this->tableCellpadding;
    }

    /**
     * @param $tableCellpadding
     * @return void
     */
    public function setTableCellpadding($tableCellpadding)
    {
        $this->tableCellpadding = $tableCellpadding;
    }

    /**
     * @return integer
     */
    public function getTableBgColor()
    {
        return $this->tableBgColor;
    }

    /**
     * @param $tableBgColor
     * @return void
     */
    public function setTableBgColor($tableBgColor)
    {
        $this->tableBgColor = $tableBgColor;
    }

    /**
     * @return integer
     */
    public function getSectionIndex()
    {
        return $this->sectionIndex;
    }

    /**
     * @param $sectionIndex
     * @return void
     */
    public function setSectionIndex($sectionIndex)
    {
        $this->sectionIndex = $sectionIndex;
    }

    /**
     * @return integer
     */
    public function getLinkToTop()
    {
        return $this->linkToTop;
    }

    /**
     * @param $linkToTop
     * @return void
     */
    public function setLinkToTop($linkToTop)
    {
        $this->linkToTop = $linkToTop;
    }

    /**
     * @return integer
     */
    public function getFilelinkSize()
    {
        return $this->filelinkSize;
    }

    /**
     * @param $filelinkSize
     * @return void
     */
    public function setFilelinkSize($filelinkSize)
    {
        $this->filelinkSize = $filelinkSize;
    }

    /**
     * @return integer
     */
    public function getSectionFrame()
    {
        return $this->sectionFrame;
    }

    /**
     * @param $sectionFrame
     * @return void
     */
    public function setSectionFrame($sectionFrame)
    {
        $this->sectionFrame = $sectionFrame;
    }

    /**
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param $date
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return integer
     */
    public function getImageFrames()
    {
        return $this->imageFrames;
    }

    /**
     * @param $imageFrames
     * @return void
     */
    public function setImageFrames($imageFrames)
    {
        $this->imageFrames = $imageFrames;
    }

    /**
     * @return integer
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param $recursive
     * @return void
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return integer
     */
    public function getRteEnabled()
    {
        return $this->rteEnabled;
    }

    /**
     * @param $rteEnabled
     * @return void
     */
    public function setRteEnabled($rteEnabled)
    {
        $this->rteEnabled = $rteEnabled;
    }

    /**
     * @return integer
     */
    public function getTxImpexpOriguid()
    {
        return $this->txImpexpOriguid;
    }

    /**
     * @param $txImpexpOriguid
     * @return void
     */
    public function setTxImpexpOriguid($txImpexpOriguid)
    {
        $this->txImpexpOriguid = $txImpexpOriguid;
    }

    /**
     * @return integer
     */
    public function getAccessibilityBypass()
    {
        return $this->accessibilityBypass;
    }

    /**
     * @param $accessibilityBypass
     * @return void
     */
    public function setAccessibilityBypass($accessibilityBypass)
    {
        $this->accessibilityBypass = $accessibilityBypass;
    }

    /**
     * @return integer
     */
    public function getSysLanguageUid()
    {
        return $this->sysLanguageUid;
    }

    /**
     * @param $sysLanguageUid
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;
    }

    /**
     * @return integer
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * @param $starttime
     * @return void
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * @return integer
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * @param $endtime
     * @return void
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * @return string
     */
    public function getTxGridelementsBackendLayout()
    {
        return $this->txGridelementsBackendLayout;
    }

    /**
     * @param $txGridelementsBackendLayout
     * @return void
     */
    public function setTxGridelementsBackendLayout($txGridelementsBackendLayout)
    {
        $this->txGridelementsBackendLayout = $txGridelementsBackendLayout;
    }

    /**
     * @return integer
     */
    public function getTxGridelementsChildren()
    {
        return $this->txGridelementsChildren;
    }

    /**
     * @param $txGridelementsChildren
     * @return void
     */
    public function setTxGridelementsChildren($txGridelementsChildren)
    {
        $this->txGridelementsChildren = $txGridelementsChildren;
    }

    /**
     * @return integer
     */
    public function getTxGridelementsContainer()
    {
        return $this->txGridelementsContainer;
    }

    /**
     * @param $txGridelementsContainer
     * @return void
     */
    public function setTxGridelementsContainer($txGridelementsContainer)
    {
        $this->txGridelementsContainer = $txGridelementsContainer;
    }

    /**
     * @return integer
     */
    public function getTxGridelementsColumns()
    {
        return $this->txGridelementsColumns;
    }

    /**
     * @param $txGridelementsColumns
     * @return void
     */
    public function setTxGridelementsColumns($txGridelementsColumns)
    {
        $this->txGridelementsColumns = $txGridelementsColumns;
    }
}
