<?php

require_once "phing/tasks/ext/SymlinkTask.php";
class PhingRelativePath extends SymlinkTask {
  private function getRelativePath($from, $to) {
   // add because we use it as base dir
   $from = realpath($from)."/";
   $to = realpath($to);
   $from = explode('/', $from);
   $to = explode('/', $to);
   foreach($from as $depth => $dir)
   {

        if(isset($to[$depth]))
        {
            if($dir === $to[$depth])
            {
               unset($to[$depth]);
               unset($from[$depth]);
            }
            else
            {
               break;
            }
        }
    }
    //$rawresult = implode('/', $to);
    for($i=0;$i<count($from)-1;$i++)
    {
        array_unshift($to,'..');
    }
    $result = implode('/', $to);
    return $result;
  }

    /**
     * Create the actual link
     *
     * @access protected
     * @param string $target
     * @param string $link
     * @return bool
     */
    protected function symlink($target, $link)
    {
        if (file_exists($link)) {
            if (!is_link($link)) {
                $this->log('File exists: ' . $link, Project::MSG_ERR);
                return false;
            }

            if (readlink($link) == $target || !$this->getOverwrite()) {
                $this->log('Link exists: ' . $link, Project::MSG_ERR);
                return false;
            }

            unlink($link);
        }

        $this->log('Linking: ' . $target . ' to ' . $link, Project::MSG_INFO);
        $base_dir = dirname($link);
        $link = basename($link);
        $rel_path = $this->getRelativePath($base_dir, $target);
        return exec("cd $base_dir; ln -s $rel_path $link");
    }
}