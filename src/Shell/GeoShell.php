<?php
namespace Geo\Shell;

use Cake\Console\Shell;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;

/**
 * Import shell command.
 */
class GeoShell extends Shell
{

    public function startup()
    {
        $this->GeoImport = TableRegistry::get('GeoImport');
        $this->GeoCountries = TableRegistry::get('Geo.Countries');
        $this->GeoI18n = TableRegistry::get('Geo.I18n');
    }

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()
            ->addOption('locale', ['short' => 'l', 'help' => 'locale to seed. ex : fr_FR, de_DE, en_US, ...'])
            ->addOption('alias', ['short' => 'a', 'help' => 'alias of the seeded locale. ex : fr, de, en, ...'])
            ->addSubcommand('seed', [
                'help' => 'Seed default locale',
                'parser' => [
                    'description' => [
                        "Seed the application default locale",
                    ],
                    'arguments' => [
                        'type' => ['help' => "Type of data to build (countries)", 'required' => true],
                    ],
                ]
            ])
            ->addSubcommand('build', [
                'help' => "building from table. It's a dev method, don't use it",
                'parser' => [
                    'arguments' => [
                        'type' => ['help' => "Type of data to build", 'required' => true],
                        'locale' => ['help' => "Locale to build", 'required' => true],
                    ],
                ],
            ])
            ;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->out();
        $this->out('<info>Geo plugin Shell</info>');
        $this->hr();
        $this->out("<info>Available commands</info>");
        $this->out("- <debug>seed</debug> : seed GeoCountries with App locale");
        $this->out("- <debug>i18n</debug> : seed Geo I18n table with locale option (-h for help)");
        $this->hr();
    }

    /**
     * Seed countri
     * @return [type] [description]
     */
    public function seed($type)
    {
        $method = '_seed' . ucfirst(strtolower($type));
        if (method_exists($this, $method)) {
            $this->{$method}();
        }
    }

    /**
     * build import files from database
     * @param  string $type   table to import
     * @param  [type] $locale [description]
     * @return [type]         [description]
     */
    public function build($type, $locale)
    {
        $db = ConnectionManager::get('default');
        $db->execute('SET FOREIGN_KEY_CHECKS = 0; TRUNCATE geo_countries; TRUNCATE geo_i18n; SET FOREIGN_KEY_CHECKS = 1;');

        $method = '_build' . ucfirst(strtolower($type));
        if (method_exists($this, $method)) {
            $this->{$method}($db, $locale);
        }
    }

    private function _seedCountries()
    {
        $filepath = Plugin::path('Geo') . 'resources' . DS . I18n::locale() . '.countries.php';

        if (!file_exists($filepath)) {
            $this->error("Locale " . I18n::locale() . " Not Found");
            return;
        }

        $datas = include $filepath;

        $hasError = false;
        foreach ($datas as $data) {
            try {

            } catch (Exception $e) {

            }
            $entity = $this->GeoCountries->newEntity($data);
            $hasError = $hasError || !$this->GeoCountries->save($entity);
        }

        if ($hasError) {
            $this->error("An error occured during seed Countries for " . I18n::locale());
        }

        return $hasError;
    }

    /**
     * [_buildCountries description]
     * @param  [type] $db     [description]
     * @param  [type] $locale [description]
     * @return [type]         [description]
     */
    private function _buildCountries($db, $locale)
    {
        $countries = $this->GeoImport->find();

        $content = <<<EOF
<?php

/**
 * Geo Locale file for $locale Countries
 * @package Erwane/CakePHP-Geo
 * @link https://github.com/erwane/cakephp-geo The CakePHP-Geo GitHub project
 * @author Erwane Breton
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

return [

EOF;

        foreach ($countries as $country) {
            $content .= sprintf("    ['id' => %d, 'title' => \"%s\", 'alpha2' => \"%s\", 'alpha3' => \"%s\", 'currency' => \"%s\", 'sort' => 65535]," . PHP_EOL,
                $country->id,
                $country->title,
                $country->alpha2,
                $country->alpha3,
                $country->currency
            );
        }

        $content .= '];' . PHP_EOL;

        $filepath = Plugin::path('Geo') . 'resources' . DS . $locale . '.countries.php';
        file_put_contents($filepath, $content);
    }
}
