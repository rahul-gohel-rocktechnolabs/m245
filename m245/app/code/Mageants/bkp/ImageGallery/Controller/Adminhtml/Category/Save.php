<?php
/**
 * @category Mageants ImageGallery
 * @package Mageants_ImageGallery
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\ImageGallery\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Save extends \Mageants\ImageGallery\Controller\Adminhtml\Category
{

    /**
     * { @inheritdoc }
     */
    protected function _isAllowed()
    {
        return true;
    }
    
    /**
     * Execute
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $files = $this->request->getFiles()->toArray(); // same as $_FIELS
        if (trim($data['url_key']) != '' && (!isset($data['category_id']))) {
            $categoryModel =$this->_categoryMOdel->getCollection()->addFieldToFilter('url_key', trim($data['url_key']));
            if ($categoryModel->getSize()) {
                $this->messageManager->addError(__('Url key already exist'));
                return $this->_redirect('*/*/');
            }
        }
        if ($data) {
            if (isset($data['image']['delete']) == 1) {
                unset($data['image']['value']);
                $data['image']['value'] = '';
            }

            $model = $this->_categoryMOdel;
            if (isset($data['category_image'])) {
                $data['image_id']=$this->_CategoryHelper->getImageId($data['category_image']);
            }

            if (isset($data['category_video'])) {
                $data['video_id']=$this->_CategoryHelper->getVideoId($data['category_video']);
            }

            if (isset($files['image']['name']) && $files['image']['name'] != '') {
                $destinationPath=$this->directory_list->getPath('media')."/imagegallery/category/images/";
                try {
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image'])
                            ->setAllowCreateFolders(true);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $result=$uploader->save($destinationPath);
                    if (!empty($result['file'])) {
                        $data['image'] ='imagegallery/category/images'.$result['file'];
                    } else {
                        $this->messageManager->addError(__('Can not save the Image Category : '.$e->getMessage()));
                        $this->_redirect('*/*/');
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError(
                        __('Please select only Image')
                    );
                    $id = $this->getRequest()->getParam('category_id');
                    if ($id) {
                        return $this->_redirect('*/*/edit', ['id' => $id]);
                    }
                    return $this->_redirect('*/*/new');
                }
            } else {
                if (isset($data['image'])) {
                    $data['image'] = $data['image']['value'];
                }
            }
            $id = $this->getRequest()->getParam('category_id');
            
            $data['url_key'] = trim($data['url_key']);
            $model->setData($data);
            
            if ($id) {
                $model->setId($id);
            }
            
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The category has been saved.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['category_id' => $this->getRequest()->getParam('category_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
