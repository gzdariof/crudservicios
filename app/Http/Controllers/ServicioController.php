<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function __construct(protected ServicioService $servicioService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $servicios = $this->servicioService->getAll();
        return view('servicios.index', compact('servicios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('servicios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'codigo'               => 'required|string|max:10',
            'nombre'               => 'required|string|max:500',
            'precio'               => 'required|numeric|min:0',
            'estado'               => 'required|boolean',
            'requiereAutorizacion' => 'required|boolean',
        ]);

        $payload = [
            'codigo'               => $validated['codigo'],
            'nombre'               => $validated['nombre'],
            'precio'               => (float) $validated['precio'],
            'estado'               => (bool) $validated['estado'],
            'requiereAutorizacion' => (bool) $validated['requiereAutorizacion'],
        ];

        $result = $this->servicioService->create($payload);

        if ($result['success']) {
            return redirect()->route('servicios.index')
                ->with('success', 'Servicio creado correctamente.');
        }

        return back()->withInput()
            ->with('error', 'No se pudo crear el servicio: ' . ($result['message'] ?? 'Error desconocido'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $servicio = $this->servicioService->getById($id);

        if (!$servicio) {
            abort(404, 'Servicio no encontrado.');
        }

        return view('servicios.show', compact('servicio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $servicio = $this->servicioService->getById($id);

        if (!$servicio) {
            abort(404, 'Servicio no encontrado.');
        }

        return view('servicios.edit', compact('servicio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validated = $request->validate([
            'codigo'               => 'required|string|max:10',
            'nombre'               => 'required|string|max:500',
            'precio'               => 'required|numeric|min:0',
            'estado'               => 'required|boolean',
            'requiereAutorizacion' => 'required|boolean',
        ]);

        $payload = [
            'id'                   => $id,
            'codigo'               => $validated['codigo'],
            'nombre'               => $validated['nombre'],
            'precio'               => (float) $validated['precio'],
            'estado'               => (bool) $validated['estado'],
            'requiereAutorizacion' => (bool) $validated['requiereAutorizacion'],
        ];

        $result = $this->servicioService->update($id, $payload);

        if ($result['success']) {
            return redirect()->route('servicios.index')
                ->with('success', 'Servicio actualizado correctamente.');
        }

        return back()->withInput()
            ->with('error', 'No se pudo actualizar el servicio: ' . ($result['message'] ?? 'Error desconocido'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $result = $this->servicioService->delete($id);

        if ($result['success']) {
            return redirect()->route('servicios.index')
                ->with('success', 'Servicio eliminado correctamente.');
        }

        return redirect()->route('servicios.index')
            ->with('error', 'No se pudo eliminar el servicio: ' . ($result['message'] ?? 'Error desconocido'));
    }
}
