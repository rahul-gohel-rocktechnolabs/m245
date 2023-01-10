<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Video;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
    /**
     * @var \Mageants\ImageGallery\Model\Video
     */
    protected $_videoModel;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directory_list;
    /**
     * @var \Mageants\ImageGallery\Helper\Data
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Backend\Model\Session
     */
    public $session;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     * @param Filesystem $fileSystem
     * @param \Mageants\ImageGallery\Model\Video $videoModel
     * @param \Mageants\ImageGallery\Helper\Data $imageHelper
     * @param \Magento\Backend\Model\Session $session
     * @param Request $request
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        Filesystem $fileSystem,
        \Mageants\ImageGallery\Model\Video $videoModel,
        \Mageants\ImageGallery\Helper\Data $imageHelper,
        \Magento\Backend\Model\Session $session,
        Request $request,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->_videoModel = $videoModel;
        $this->directory_list = $directory_list;
        $this->_imageHelper = $imageHelper;
        $this->session = $session;
        $this->request = $request;
        parent::__construct($context);
        $success = $this->checkPostSizeExceeded();
        if ($success == 'false') {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
    }
    /**
     * Check Post size
     *
     * @return bool
     */
    public function checkPostSizeExceeded()
    {
        $maxPostSize = $this->iniGetBytes('post_max_size');
        
        if (${'_SERVER'}['CONTENT_LENGTH'] > $maxPostSize) {
            $this->messageManager->addError(
                __('Max post size exceeded! Please increase your post_max_size and
                    upload_max_filesize in php configuration file.')
            );
            return 'false';
        }
        return 'true';
    }
    /**
     * Ini get bytes
     *
     * @param int $val
     * @return int
     */
    public function iniGetBytes($val)
    {
        $val = trim(ini_get($val));
        if ($val != '') {
            $last = strtolower($val[strlen($val) - 1]);
            $val = str_replace($val[strlen($val) - 1], '', $val);
        } else {
            $last = '';
        }
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
                // fall through
            case 'm':
                $val *= 1024;
                // fall through
            case 'k':
                $val *= 1024;
                // fall through
        }
        return $val;
    }
    /**
     * Execute
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $files = $this->request->getFiles()->toArray(); // same as $_FIELS
        if ($data) {
            $model = $this->_videoModel;
            if (isset($data['video']['delete']) == 1) {
                if (!isset($files['video']['tmp_name']) || empty($files['video']['tmp_name'])) {
                    $this->messageManager->addError('Please select another video to delete current one');
                } else {
                    unset($data['video']['value']);
                    $data['video']['value'] = '';
                }
            }
            if (isset($files['video']['name']) && $files['video']['name'] != '') {
                $destinationPath=$this->directory_list->getPath('media')."/imagegallery/gallery/video/";
                try {
                    try {
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'video'])
                                    ->setAllowCreateFolders(true);
                            $uploader->setAllowedExtensions(['mp4', 'avi','webm','flv','wmv','3gp','ogg']);
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(true);
                    } catch (\Exception $e) {
                        $this->messageManager->addError(
                            __('Max Filesize exceeded! Please increase your upload_max_filesize 
                                in php configuration file.Otherwise upload video less than 2MB!')
                        );
                        $this->_redirect('*/*/');
                    }

                    if ($this->_fileUploaderFactory->create(['fileId' => 'video'])->getFileSize(true) >= 2000000) {
                        $this->messageManager->addError("Video size is too big");
                    } else {
                        $result=$uploader->save($destinationPath);
                    }
                    if (!empty($result['file'])) {
                        $data['video'] ='imagegallery/gallery/video'.$result['file'];
                    } else {
                        $this->messageManager->addError(__('Can not save the Category icon: '.$e->getMessage()));
                        $this->_redirect('*/*/');
                    }
                } catch (\Exception $e) {
                    $id = $this->getRequest()->getParam('video_id');
                    if ($id) {
                        return $this->_redirect('*/*/edit', ['id' => $id]);
                    }
                    return $this->_redirect('*/*/new');
                }
            } else {
                if (isset($data['video'])) {
                    $data['video'] = $data['video']['value'];
                }
            }

            if ($this->getRequest()->getParam('video_id')) {
                $id = $this->getRequest()->getParam('video_id');
            } else {
                $id = $this->session->getVideoId();
            }

            if ($id) {
                $model->load($id);
            }

            $model->addData($data);
            
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The video has been saved.'));
                $this->session->setFormData(false);
                $this->session->setVideoId($model->getVideoId());
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getVideoId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the video.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('video_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
