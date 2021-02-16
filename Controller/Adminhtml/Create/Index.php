<?php
namespace Bss\CreateMenuBackend\Controller\Adminhtml\Create;
class Index extends \Magento\Backend\App\Action
{
         protected $resultPageFactory = false;      
         public function __construct(
                 \Magento\Backend\App\Action\Context $context,
                 \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                 \Magento\Framework\Filesystem\DirectoryList $dir,
                 \Magento\Framework\Filesystem\Driver\File $driverFile,
                 \Magento\Framework\Filesystem\DirectoryList $directoryList      
         ) {
                
                 parent::__construct($context);
                 $this->resultPageFactory = $resultPageFactory;
                 $this->_dir = $dir;
                 $this->directoryList =$directoryList;
                 $this->driverFile = $driverFile;
                
         } 
         public function execute()
         {
                
                        /** @var Page $page */
                        $page = $this->resultPageFactory->create();
                        $page->setActiveMenu('Bss_CreateMenuBackend::menu');
                        $page->getConfig()->getTitle()->prepend(__('System Logs'));

                        $paths = [];
                        try {

                        $path= $this->_dir->getPath('log'); // Output: /var/www/html/myproject/var/log   
                        //read just that single directory
                        $paths =  $this->driverFile->readDirectory($path);
                        //read all folders
                        $paths =  $this->driverFile->readDirectoryRecursively($path);

                        } catch (FileSystemException $e) {
                        $this->logger->error($e->getMessage());
                        }

                        $logfile=[];

                        //let loop over paths to determine size of each file 
                        for ($i=0; $i <= count($paths)-1; $i++) { 
                                $logfile[$i]=["path"=>$paths[$i], "size"=>stat($paths[$i])['size']];
                        }
                        /** @var Template $block */
                        $block = $page->getLayout()->getBlock('logfiles');

                        array_multisort(array_column($logfile, 'size'), SORT_DESC, $logfile);

                        $block->setData("log_files", $logfile);
                        
                       
                        return $page;

         }
         protected function _isAllowed()
         {
                 return $this->_authorization->isAllowed('Bss_CreateMenuBackend::menu');
         }


}