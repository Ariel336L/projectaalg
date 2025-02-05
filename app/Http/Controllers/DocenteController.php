<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{

    public function index(Request $request)
    {
        $query = Docente::query();

        if ($request->has('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->has('apellido')) {
            $query->where('apellido', 'like', '%' . $request->apellido . '%');
        }

        $docentes = $query->orderBy('id', 'desc')->simplePaginate(10);

        return view('docente.index', compact('docentes'));
    }

    public function create()
    {
        return view('docente.create');
    }

    public function store(Request $request)
    {
        $request->merge(['password' => Hash::make($request->password)]);
        $docente = Docente::create($request->all());

        return redirect()->route('docentes.index')->with('success', 'DOCENTE CREADO CORRECTAMENTE');
    }

    public function show($id)
    {
        $docente = Docente::find($id);

        if (!$docente) {
            return abort(404);
        }

        return view('docente.show', compact('docente'));
    }

    public function edit($id)
    {
        $docente = Docente::find($id);

        if (!$docente) {
            return abort(404);
        }

        return view('docente.edit', compact('docente'));
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::find($id);

        if (!$docente) {
            return abort(404);
        }

        $docente->nombre = $request->nombre;
        $docente->apellido = $request->apellido;
        $docente->email = $request->email;
        
        $docente->save();

        return redirect()->route('docentes.index')->with('success', 'DOCENTE MODIFICADO CORRECTAMENTE');
    }

    public function delete($id)
    {
        $docente = Docente::find($id);

        if (!$docente) {
            return abort(404);
        }

        return view('docente.delete', compact('docente'));
    }

    public function destroy($id)
    {
        $docente = Docente::find($id);

        if (!$docente) {
            return abort(404);
        }

        $docente->delete();

        return redirect()->route('docentes.index')->with('success', 'DOCENTE ELIMINADO CORRECTAMENTE');
    }

    public function showLoginForm()
    {
        return view('docente.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('docente')->attempt($credentials)) {
            return redirect()->intended('/');
        }

        return redirect()->back()->withErrors([
            'InvalidCredentials' => 'LAS CREDENCIALES PROPORCIONADAS NO COINCIDEN CON NUESTROS REGISTROS.',
        ]);
    }

    public function logout()
    {
        Auth::guard('docente')->logout();

        return redirect()->route('docentes.showLoginForm');
    }
}