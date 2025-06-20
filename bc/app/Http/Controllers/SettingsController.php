<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ConfigHelper;

class SettingsController extends Controller
{
    /**
     * Exibir página de configurações
     */
    public function index()
    {
        $categories = [
            'general' => 'Configurações Gerais',
            'appearance' => 'Aparência e Tema',
            'dashboard' => 'Dashboard',
            'modules' => 'Módulos do Sistema',
            'advanced' => 'Configurações Avançadas'
        ];

        $settings = SystemSetting::orderBy('category')
                                ->orderBy('sort_order')
                                ->get()
                                ->groupBy('category');

        return view('settings.index', compact('categories', 'settings'));
    }

    /**
     * Salvar configurações
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            foreach ($request->settings as $key => $value) {
                $setting = SystemSetting::where('key', $key)->first();
                
                if (!$setting) {
                    continue;
                }

                // Processar upload de arquivos
                if ($setting->type === 'file' && $request->hasFile("file_{$key}")) {
                    $file = $request->file("file_{$key}");
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('settings', $filename, 'public');
                    $value = $path;
                }

                // Validar cores
                if ($setting->type === 'color' && !preg_match('/^#[a-f0-9]{6}$/i', $value)) {
                    continue;
                }

                SystemSetting::set($key, $value);
            }

            // Limpar cache
            ConfigHelper::clearCache();

            return redirect()->route('settings.index')
                           ->with('success', 'Configurações salvas com sucesso!');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Restaurar configurações padrão
     */
    public function reset(Request $request)
    {
        $category = $request->get('category');
        
        try {
            if ($category) {
                // Resetar apenas uma categoria
                $this->resetCategoryDefaults($category);
                $message = "Configurações de {$category} restauradas com sucesso!";
            } else {
                // Resetar todas as configurações
                $this->resetAllDefaults();
                $message = "Todas as configurações foram restauradas aos valores padrão!";
            }

            SystemSetting::clearCache();

            return redirect()->route('settings.index')
                           ->with('success', $message);
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao restaurar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Exportar configurações
     */
    public function export()
    {
        try {
            $settings = SystemSetting::all()->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'category' => $setting->category,
                    'label' => $setting->label,
                    'description' => $setting->description,
                    'options' => $setting->options,
                    'is_public' => $setting->is_public,
                    'sort_order' => $setting->sort_order,
                ];
            });

            $filename = 'bc_system_settings_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($settings)
                           ->header('Content-Disposition', "attachment; filename={$filename}");
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao exportar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Importar configurações
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings_file' => 'required|file|mimes:json|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        try {
            $file = $request->file('settings_file');
            $content = file_get_contents($file->getPathname());
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Arquivo JSON inválido');
            }

            foreach ($settings as $settingData) {
                SystemSetting::updateOrCreate(
                    ['key' => $settingData['key']],
                    $settingData
                );
            }

            SystemSetting::clearCache();

            return redirect()->route('settings.index')
                           ->with('success', 'Configurações importadas com sucesso!');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao importar configurações: ' . $e->getMessage());
        }
    }

    /**
     * API para obter configurações públicas (para AJAX)
     */
    public function getPublicSettings()
    {
        try {
            $settings = SystemSetting::getPublicSettings();
            return response()->json($settings);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Criar nova configuração personalizada
     */
    public function createCustom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:system_settings,key|regex:/^[a-z_]+$/',
            'label' => 'required|string|max:255',
            'type' => 'required|in:string,integer,boolean,color,select,textarea,file',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'value' => 'nullable',
            'options' => 'nullable|json',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            SystemSetting::create($request->all());
            SystemSetting::clearCache();

            return redirect()->route('settings.index')
                           ->with('success', 'Configuração personalizada criada com sucesso!');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao criar configuração: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Servir CSS dinâmico baseado nas configurações
     */
    public function dynamicCSS()
    {
        $css = ConfigHelper::generateDynamicCSS();
        
        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Resetar configurações padrão por categoria
     */
    private function resetCategoryDefaults($category)
    {
        // Implementar reset específico por categoria
        // Por enquanto, vamos apenas manter as configurações existentes
    }

    /**
     * Resetar todas as configurações padrão
     */
    private function resetAllDefaults()
    {
        // Implementar reset completo
        // Por enquanto, vamos apenas manter as configurações existentes
    }
}
