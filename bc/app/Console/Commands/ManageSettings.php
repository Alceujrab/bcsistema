<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;
use App\Helpers\ConfigHelper;

class ManageSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:manage 
                            {action : Action to perform (list, get, set, delete, reset, clear-cache)}
                            {key? : Configuration key}
                            {value? : Configuration value}
                            {--category= : Filter by category}
                            {--force : Force action without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage system settings via command line';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $key = $this->argument('key');
        $value = $this->argument('value');
        $category = $this->option('category');
        $force = $this->option('force');

        switch ($action) {
            case 'list':
                $this->listSettings($category);
                break;
                
            case 'get':
                $this->getSetting($key);
                break;
                
            case 'set':
                $this->setSetting($key, $value, $force);
                break;
                
            case 'delete':
                $this->deleteSetting($key, $force);
                break;
                
            case 'reset':
                $this->resetSettings($force);
                break;
                
            case 'clear-cache':
                $this->clearCache();
                break;
                
            default:
                $this->error("Invalid action: $action");
                $this->info('Available actions: list, get, set, delete, reset, clear-cache');
                return 1;
        }

        return 0;
    }

    /**
     * List all settings or by category
     */
    protected function listSettings($category = null)
    {
        $query = SystemSetting::orderBy('category')->orderBy('sort_order');
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $settings = $query->get();
        
        if ($settings->isEmpty()) {
            $this->info('No settings found.');
            return;
        }

        $this->table(
            ['Key', 'Value', 'Type', 'Category', 'Label'],
            $settings->map(function ($setting) {
                return [
                    $setting->key,
                    is_string($setting->value) && strlen($setting->value) > 50 
                        ? substr($setting->value, 0, 47) . '...' 
                        : $setting->value,
                    $setting->type,
                    $setting->category,
                    $setting->label
                ];
            })
        );
        
        $this->info("Total: " . $settings->count() . " settings");
    }

    /**
     * Get a specific setting
     */
    protected function getSetting($key)
    {
        if (!$key) {
            $this->error('Key is required for get action');
            return;
        }

        $setting = SystemSetting::where('key', $key)->first();
        
        if (!$setting) {
            $this->error("Setting '$key' not found.");
            return;
        }

        $this->info("Key: {$setting->key}");
        $this->info("Value: {$setting->value}");
        $this->info("Type: {$setting->type}");
        $this->info("Category: {$setting->category}");
        $this->info("Label: {$setting->label}");
        $this->info("Description: {$setting->description}");
    }

    /**
     * Set a setting value
     */
    protected function setSetting($key, $value, $force = false)
    {
        if (!$key) {
            $this->error('Key is required for set action');
            return;
        }

        if (!$value) {
            $this->error('Value is required for set action');
            return;
        }

        $setting = SystemSetting::where('key', $key)->first();
        
        if (!$setting) {
            $this->error("Setting '$key' not found.");
            return;
        }

        if (!$force && !$this->confirm("Set '{$setting->label}' to '$value'?")) {
            $this->info('Cancelled.');
            return;
        }

        $oldValue = $setting->value;
        $setting->value = $value;
        $setting->save();

        ConfigHelper::clearCache($key);

        $this->info("Setting '{$setting->label}' updated:");
        $this->info("  Old value: $oldValue");
        $this->info("  New value: $value");
    }

    /**
     * Delete a setting
     */
    protected function deleteSetting($key, $force = false)
    {
        if (!$key) {
            $this->error('Key is required for delete action');
            return;
        }

        $setting = SystemSetting::where('key', $key)->first();
        
        if (!$setting) {
            $this->error("Setting '$key' not found.");
            return;
        }

        if (!$force && !$this->confirm("Delete setting '{$setting->label}'?")) {
            $this->info('Cancelled.');
            return;
        }

        $setting->delete();
        ConfigHelper::clearCache($key);

        $this->info("Setting '{$setting->label}' deleted successfully.");
    }

    /**
     * Reset all settings to defaults
     */
    protected function resetSettings($force = false)
    {
        if (!$force && !$this->confirm('Reset ALL settings to defaults? This cannot be undone!')) {
            $this->info('Cancelled.');
            return;
        }

        SystemSetting::truncate();
        ConfigHelper::clearCache();

        $this->call('db:seed', ['--class' => 'SystemSettingsSeeder']);

        $this->info('All settings reset to defaults successfully.');
    }

    /**
     * Clear settings cache
     */
    protected function clearCache()
    {
        ConfigHelper::clearCache();
        $this->info('Settings cache cleared successfully.');
    }
}
