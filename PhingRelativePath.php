<?php

require_once "phing/tasks/ext/SymlinkTask.php";
class PhingRelativePath extends SymlinkTask {
  private function getRelativePath($from, $to) {
    $from = explode('/', $from);
    $to = explode('/', $to);
    $relPath = $to;

    foreach ($from as $depth => $dir) {
      // find first non-matching dir
      if ($dir === $to[$depth]) {
        // ignore this directory
        array_shift($relPath);
      }
      else {
        // get number of remaining dirs to $from
        $remaining = count($from) - $depth;
        if ($remaining > 1) {
          // add traversals up to first matching dir
          $padLength = (count($relPath) + $remaining - 1) * -1;
          $relPath = array_pad($relPath, $padLength, '..');
          break;
        }
        else {
          $relPath[0] = './' . $relPath[0];
        }
      }
    }
    return implode('/', $relPath);
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

        $fs = FileSystem::getFileSystem();

        $this->log('Linking: ' . $target . ' to ' . $link, Project::MSG_INFO);

        $link = $this->getRelativePath($link, $target);
        return $fs->symlink($target, $link);
    }
}