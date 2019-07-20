<?php declare(strict_types=1);


namespace Core\Config\Parser;


use function class_exists;
use SplFileInfo;
use Core\Config\Config;
use Stdlib\Helper\Arr;
use Stdlib\Helper\ArrayHelper;
use Stdlib\Helper\DirectoryHelper;
use Swoole\Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlParser
 */
class YamlParser extends Parser
{
    /**
     * @param Config $config
     *
     * @return array
     * @throws ConfigException
     */
    public function parse(Config $config): array
    {
        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            throw new ConfigException('You must to composer require symfony/yaml');
        }

        $base         = $config->getBase();
        $path         = $config->getPath();
        $env          = $config->getEnv();
        $envPath      = sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $env);
        $baseFileName = sprintf('%s.%s', $base, Config::TYPE_YAML);

        $yamlConfig = $this->getConfig($baseFileName, $path);

        if (!empty($env) && !file_exists($envPath)) {
            throw new Exception(
                sprintf('Env path(%s) is not exist!', $envPath)
            );
        }

        if (!empty($env)) {
            $envConfig  = $this->getConfig($baseFileName, $envPath);
            $yamlConfig = Arr::merge($yamlConfig, $envConfig);
        }

        return $yamlConfig;
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

            if ($ext != Config::TYPE_YAML) {
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
                $baseConfig = Yaml::parseFile($filePath);
                continue;
            }

            // Other config
            [$key] = explode('.', $fileName);
            $data = Yaml::parseFile($filePath);

            ArrayHelper::set($otherConfig, $key, $data);
        }

        return ArrayHelper::merge($baseConfig, $otherConfig);
    }
}