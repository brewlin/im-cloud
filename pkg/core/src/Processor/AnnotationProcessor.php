<?php

namespace Core\Processor;

use Exception;
use Core\Annotation\AnnotationRegister;
use Log\Helper\CLog;

/**
 * Annotation processor
 * @since 2.0
 */
class AnnotationProcessor extends Processor
{
    /**
     * Handle annotation
     *
     * @return bool
     * @throws Exception
     */
    public function handle(): bool
    {

        // Find AutoLoader classes. Parse and collect annotations.
        AnnotationRegister::load([
            'basePath'             => ROOT."/app",
            'notifyHandler'        => [$this, 'notifyHandler'],
        ]);
        $stats = AnnotationRegister::getClassStats();

        CLog::debug(
            'Annotations is scanned(autoloader %d, annotation %d, parser %d)',
            $stats['autoloader'],
            $stats['annotation'],
            $stats['parser']
        );
        return true;

    }

    /**
     * @param string $type
     * @param string $target
     * @see \Core\Annotation\Resource\AnnotationResource::load()
     */
    public function notifyHandler(string $type, $target): void
    {
        switch ($type) {
            case 'excludeNs':
//                CLog::debug('Exclude namespace %s', $target);
                break;
            case 'noLoaderFile':
                CLog::debug('No autoloader on %s', $target);
                break;
            case 'noLoaderClass':
                CLog::debug('Autoloader class not exist %s', $target);
                break;
            case 'findLoaderClass':
//                CLog::debug('Find autoloader %s', $target);
                break;
            case 'addLoaderClass':
                CLog::debug('Parse autoloader %s', $target);
                break;
            case 'noExistClass':
//                CLog::debug('Not exist class %s', $target);
                break;
        }
    }
}
