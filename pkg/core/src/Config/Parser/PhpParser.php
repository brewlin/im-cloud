<?php declare(strict_types=1);

namespace Core\Config\Parser;


use SplFileInfo;
use Core\Config\Config;
use Stdlib\Helper\Arr;
use Stdlib\Helper\ArrayHelper;
use Stdlib\Helper\DirectoryHelper;
use function is_dir;
use Swoole\Exception;

/**
 * Class PhpParser
 */
class PhpParser extends Parser
{
    /**
     * Parse php files
     *
     * @param Config $config
     *
     * @return array
     */
    public function parse(Config $config): array
    {
        $base         = $config->getBase();
        $envPath          = $config->getEnv();
        $path         = $config->getPath();
        $baseFileName = sprintf('%s.%s', $base, Config::TYPE_PHP);

        $phpConfig = $this->getConfig($baseFileName, $path);

        if (!empty($env) && !file_exists($envPath)) {
            throw new Exception(
                sprintf('Env path(%s) is not exist!', $envPath)
            );
        }

        if (!empty($env)) {
            $envConfig = $this->getConfig($baseFileName, $envPath);
            $phpConfig = Arr::merge($phpConfig, $envConfig);
        }

        return $phpConfig;
    }

    /**
     * @param string $baseFileName
     * @param string $path
     *
     * @return array
     */
    protected function getConfig(string $baseFileName, string $path): array
    {
        $iterator = DirectoryHelper::iterator($path);

        $baseConfig  = [];
        $otherConfig = [];

        /* @var SplFileInfo $splFileInfo */
        foreach ($iterator as $splFileInfo) {

            // Ingore other extension file
            $ext = $splFileInfo->getExtension();
            $ext = strtolower($ext);

            if ($ext != Config::TYPE_PHP) {
                continue;
            }

            $fileName = $splFileInfo->getFilename();
            $fileName = strtolower($fileName);
            $filePath = $splFileInfo->getPathname();

            // Exclude dir
            if (is_dir($filePath)) {
                continue;
            }

            // Base config
            if ($fileName == $baseFileName) {
                $baseConfig = require $filePath;
                continue;
            }

            // Other config
            [$key] = explode('.', $fileName);
            $data = require $filePath;

            ArrayHelper::set($otherConfig, $key, $data);
        }

        return ArrayHelper::merge($baseConfig, $otherConfig);
    }
}