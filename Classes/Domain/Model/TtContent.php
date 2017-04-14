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
     * @var int
     */
    protected $colPos;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var int
     */
    protected $imagewidth;

    /**
     * @var int
     */
    protected $imageheight;

    /**
     * @var int
     */
    protected $imageorient;

    /**
     * @var string
     */
    protected $imagecaption;

    /**
     * @var int
     */
    protected $imagecols;

    /**
     * @var int
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
     * @var int
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
     * @var int
     */
    protected $spaceBefore;

    /**
     * @var int
     */
    protected $spaceAfter;

    /**
     * @var int
     */
    protected $imageNoRows;

    /**
     * @var int
     */
    protected $imageEffects;

    /**
     * @var int
     */
    protected $imageCompression;

    /**
     * @var int
     */
    protected $tableBorder;

    /**
     * @var int
     */
    protected $tableCellspacing;

    /**
     * @var int
     */
    protected $tableCellpadding;

    /**
     * @var int
     */
    protected $tableBgColor;

    /**
     * @var int
     */
    protected $sectionIndex;

    /**
     * @var int
     */
    protected $linkToTop;

    /**
     * @var int
     */
    protected $filelinkSize;

    /**
     * @var int
     */
    protected $sectionFrame;

    /**
     * @var int
     */
    protected $date;

    /**
     * @var int
     */
    protected $imageFrames;

    /**
     * @var int
     */
    protected $recursive;

    /**
     * @var int
     */
    protected $rteEnabled;

    /**
     * @var int
     */
    protected $txImpexpOriguid;

    /**
     * @var int
     */
    protected $accessibilityBypass;

    /**
     * @var int
     */
    protected $sysLanguageUid;

    /**
     * @var int
     */
    protected $starttime;

    /**
     * @var int
     */
    protected $endtime;

    /**
     * @var string
     */
    protected $txGridelementsBackendLayout;

    /**
     * @var int
     */
    protected $txGridelementsChildren;

    /**
     * @var int
     */
    protected $txGridelementsContainer;

    /**
     * @var int
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
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get the colpos
     *
     * @return int
     */
    public function getColPos()
    {
        return (int)$this->colPos;
    }

    /**
     * Set colpos
     *
     * @param int $colPos
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
     */
    public function setCategoryField($categoryField)
    {
        $this->categoryField = $categoryField;
    }

    /**
     * @return int
     */
    public function getSpaceBefore()
    {
        return $this->spaceBefore;
    }

    /**
     * @param $spaceBefore
     */
    public function setSpaceBefore($spaceBefore)
    {
        $this->spaceBefore = $spaceBefore;
    }

    /**
     * @return int
     */
    public function getSpaceAfter()
    {
        return $this->spaceAfter;
    }

    /**
     * @param $spaceAfter
     */
    public function setSpaceAfter($spaceAfter)
    {
        $this->spaceAfter = $spaceAfter;
    }

    /**
     * @return int
     */
    public function getImageNoRows()
    {
        return $this->imageNoRows;
    }

    /**
     * @param $imageNoRows
     */
    public function setImageNoRows($imageNoRows)
    {
        $this->imageNoRows = $imageNoRows;
    }

    /**
     * @return int
     */
    public function getImageEffects()
    {
        return $this->imageEffects;
    }

    /**
     * @param $imageEffects
     */
    public function setImageEffects($imageEffects)
    {
        $this->imageEffects = $imageEffects;
    }

    /**
     * @return int
     */
    public function getImageCompression()
    {
        return $this->imageCompression;
    }

    /**
     * @param $imageCompression
     */
    public function setImageCompression($imageCompression)
    {
        $this->imageCompression = $imageCompression;
    }

    /**
     * @return int
     */
    public function getTableBorder()
    {
        return $this->tableBorder;
    }

    /**
     * @param $tableBorder
     */
    public function setTableBorder($tableBorder)
    {
        $this->tableBorder = $tableBorder;
    }

    /**
     * @return int
     */
    public function getTableCellspacing()
    {
        return $this->tableCellspacing;
    }

    /**
     * @param $tableCellspacing
     */
    public function setTableCellspacing($tableCellspacing)
    {
        $this->tableCellspacing = $tableCellspacing;
    }

    /**
     * @return int
     */
    public function getTableCellpadding()
    {
        return $this->tableCellpadding;
    }

    /**
     * @param $tableCellpadding
     */
    public function setTableCellpadding($tableCellpadding)
    {
        $this->tableCellpadding = $tableCellpadding;
    }

    /**
     * @return int
     */
    public function getTableBgColor()
    {
        return $this->tableBgColor;
    }

    /**
     * @param $tableBgColor
     */
    public function setTableBgColor($tableBgColor)
    {
        $this->tableBgColor = $tableBgColor;
    }

    /**
     * @return int
     */
    public function getSectionIndex()
    {
        return $this->sectionIndex;
    }

    /**
     * @param $sectionIndex
     */
    public function setSectionIndex($sectionIndex)
    {
        $this->sectionIndex = $sectionIndex;
    }

    /**
     * @return int
     */
    public function getLinkToTop()
    {
        return $this->linkToTop;
    }

    /**
     * @param $linkToTop
     */
    public function setLinkToTop($linkToTop)
    {
        $this->linkToTop = $linkToTop;
    }

    /**
     * @return int
     */
    public function getFilelinkSize()
    {
        return $this->filelinkSize;
    }

    /**
     * @param $filelinkSize
     */
    public function setFilelinkSize($filelinkSize)
    {
        $this->filelinkSize = $filelinkSize;
    }

    /**
     * @return int
     */
    public function getSectionFrame()
    {
        return $this->sectionFrame;
    }

    /**
     * @param $sectionFrame
     */
    public function setSectionFrame($sectionFrame)
    {
        $this->sectionFrame = $sectionFrame;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getImageFrames()
    {
        return $this->imageFrames;
    }

    /**
     * @param $imageFrames
     */
    public function setImageFrames($imageFrames)
    {
        $this->imageFrames = $imageFrames;
    }

    /**
     * @return int
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return int
     */
    public function getRteEnabled()
    {
        return $this->rteEnabled;
    }

    /**
     * @param $rteEnabled
     */
    public function setRteEnabled($rteEnabled)
    {
        $this->rteEnabled = $rteEnabled;
    }

    /**
     * @return int
     */
    public function getTxImpexpOriguid()
    {
        return $this->txImpexpOriguid;
    }

    /**
     * @param $txImpexpOriguid
     */
    public function setTxImpexpOriguid($txImpexpOriguid)
    {
        $this->txImpexpOriguid = $txImpexpOriguid;
    }

    /**
     * @return int
     */
    public function getAccessibilityBypass()
    {
        return $this->accessibilityBypass;
    }

    /**
     * @param $accessibilityBypass
     */
    public function setAccessibilityBypass($accessibilityBypass)
    {
        $this->accessibilityBypass = $accessibilityBypass;
    }

    /**
     * @return int
     */
    public function getSysLanguageUid()
    {
        return $this->sysLanguageUid;
    }

    /**
     * @param $sysLanguageUid
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;
    }

    /**
     * @return int
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * @param $starttime
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * @return int
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * @param $endtime
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
     */
    public function setTxGridelementsBackendLayout($txGridelementsBackendLayout)
    {
        $this->txGridelementsBackendLayout = $txGridelementsBackendLayout;
    }

    /**
     * @return int
     */
    public function getTxGridelementsChildren()
    {
        return $this->txGridelementsChildren;
    }

    /**
     * @param $txGridelementsChildren
     */
    public function setTxGridelementsChildren($txGridelementsChildren)
    {
        $this->txGridelementsChildren = $txGridelementsChildren;
    }

    /**
     * @return int
     */
    public function getTxGridelementsContainer()
    {
        return $this->txGridelementsContainer;
    }

    /**
     * @param $txGridelementsContainer
     */
    public function setTxGridelementsContainer($txGridelementsContainer)
    {
        $this->txGridelementsContainer = $txGridelementsContainer;
    }

    /**
     * @return int
     */
    public function getTxGridelementsColumns()
    {
        return $this->txGridelementsColumns;
    }

    /**
     * @param $txGridelementsColumns
     */
    public function setTxGridelementsColumns($txGridelementsColumns)
    {
        $this->txGridelementsColumns = $txGridelementsColumns;
    }
}
