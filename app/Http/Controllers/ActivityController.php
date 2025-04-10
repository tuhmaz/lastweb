<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = $this->getActivities(1);
        return view('content.dashboard.activities.index', compact('activities'));
    }

    public function loadMore(Request $request)
    {
        $page = $request->input('page', 1);
        $activities = $this->getActivities($page);
        
        return response()->json([
            'html' => view('content.dashboard.activities._list', compact('activities'))->render(),
            'hasMore' => count($activities) === 20
        ]);
    }

    private function getActivities($page)
    {
        // Obtener actividades reales de la base de datos usando el modelo Activity
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Filtrar solo las actividades de las últimas 24 horas
        $last24Hours = Carbon::now()->subHours(24);
        
        $activities = Activity::with('causer')
            ->where('created_at', '>=', $last24Hours)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get()
            ->map(function ($activity) {
                // Mapear los datos del modelo Activity al formato esperado por la vista
                $iconMap = [
                    'created' => 'bx-plus-circle',
                    'updated' => 'bx-edit',
                    'deleted' => 'bx-trash',
                    'login' => 'bx-log-in',
                    'logout' => 'bx-log-out',
                    'default' => 'bx-activity'
                ];
                
                $typeMap = [
                    'article' => 'article',
                    'news' => 'news',
                    'comment' => 'comment',
                    'user' => 'user',
                    'default' => 'system'
                ];
                
                // Determinar el tipo basado en el log_name o subject_type
                $type = 'default';
                if (isset($activity->subject_type)) {
                    $subjectClass = strtolower(class_basename($activity->subject_type));
                    $type = array_key_exists($subjectClass, $typeMap) ? $subjectClass : 'default';
                }
                
                // Determinar el icono basado en la descripción
                $icon = $iconMap['default'];
                foreach ($iconMap as $key => $value) {
                    if (strpos(strtolower($activity->description), $key) !== false) {
                        $icon = $value;
                        break;
                    }
                }
                
                // Obtener información del usuario si está disponible
                $userName = 'Sistema';
                // Establecer una imagen de avatar predeterminada
                $userAvatar = asset('assets/img/avatars/default-avatar.png');
                
                if ($activity->causer) {
                    $userName = $activity->causer->name ?? 'Usuario ' . $activity->causer->id;
                    // Usar la imagen de perfil del usuario si está disponible, de lo contrario usar la imagen predeterminada
                    if (isset($activity->causer->profile_photo_url) && !empty($activity->causer->profile_photo_url)) {
                        $userAvatar = $activity->causer->profile_photo_url;
                    }
                }
                
                // Construir una descripción legible
                $description = $activity->description;
                if ($activity->subject) {
                    $subjectName = '';
                    if (method_exists($activity->subject, 'getNameAttribute')) {
                        $subjectName = $activity->subject->name;
                    } elseif (isset($activity->subject->title)) {
                        $subjectName = $activity->subject->title;
                    } elseif (isset($activity->subject->name)) {
                        $subjectName = $activity->subject->name;
                    } elseif (isset($activity->properties['attributes']['title'])) {
                        $subjectName = $activity->properties['attributes']['title'];
                    } elseif (isset($activity->properties['attributes']['name'])) {
                        $subjectName = $activity->properties['attributes']['name'];
                    }
                    
                    if (!empty($subjectName)) {
                        $description .= ' "' . $subjectName . '"';
                    }
                }
                
                return [
                    'type' => $type,
                    'icon' => $icon,
                    'action' => ucfirst($activity->description),
                    'description' => $description,
                    'time' => $activity->created_at,
                    'user' => $userName,
                    'user_avatar' => $userAvatar,
                    'properties' => $activity->properties
                ];
            });
        
        return $activities;
    }
    
    // Método para limpiar actividades antiguas (puede ser llamado por un programador de tareas)
    public function cleanOldActivities()
    {
        $last24Hours = Carbon::now()->subHours(24);
        $deleted = Activity::where('created_at', '<', $last24Hours)->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Se han eliminado $deleted registros de actividad antiguos"
        ]);
    }
}
