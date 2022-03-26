<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Migrations\NameParser;
use Fresns\PluginManager\Support\Migrations\SchemaParser;
use Fresns\PluginManager\Support\Stub;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginMakeMigrationCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a migration for specified plugin.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The migration name will be created.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be created.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'The specified fields table.', null],
            ['plain', null, InputOption::VALUE_NONE, 'Create plain migration.'],
        ];
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser(): SchemaParser
    {
        return new SchemaParser($this->option('fields'));
    }

    /**
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function getTemplateContents(): string
    {
        $parser = new NameParser($this->argument('name'));
        if ($parser->isCreate()) {
            return Stub::create('/migration/create.stub', [
                'CLASS' => $this->getClass(),
                'TABLE' => $parser->getTableName(),
                'FIELDS' => $this->getSchemaParser()->render(),
            ]);
        } elseif ($parser->isAdd()) {
            return Stub::create('/migration/add.stub', [
                'CLASS' => $this->getClass(),
                'TABLE' => $parser->getTableName(),
                'FIELDS_UP' => $this->getSchemaParser()->up(),
                'FIELDS_DOWN' => $this->getSchemaParser()->down(),
            ]);
        } elseif ($parser->isDelete()) {
            return Stub::create('/migration/delete.stub', [
                'CLASS' => $this->getClass(),
                'TABLE' => $parser->getTableName(),
                'FIELDS_DOWN' => $this->getSchemaParser()->up(),
                'FIELDS_UP' => $this->getSchemaParser()->down(),
            ]);
        } elseif ($parser->isDrop()) {
            return Stub::create('/migration/drop.stub', [
                'CLASS' => $this->getClass(),
                'TABLE' => $parser->getTableName(),
                'FIELDS' => $this->getSchemaParser()->render(),
            ]);
        }

        return Stub::create('/migration/plain.stub', [
            'CLASS' => $this->getClass(),
        ]);
    }

    /**
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        $plugin = $this->getPlugin();

        $generatorPath = GenerateConfigReader::read('migration');

        return $plugin->getPath().'/'.$generatorPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return date('Y_m_d_His_').$this->getSchemaName();
    }

    /**
     * @return array|string
     */
    private function getSchemaName()
    {
        return $this->argument('name');
    }

    /**
     * @return string
     */
    private function getClassName(): string
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->getClassName();
    }

    /**
     * Run the command.
     */
    public function handle(): int
    {
        if (parent::handle() === E_ERROR) {
            return E_ERROR;
        }

        if (app()->environment() === 'testing') {
            return 0;
        }

        return 0;
    }
}
