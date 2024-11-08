<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         // Obter o parâmetro 'per_page' ou usar o valor padrão de 10
         $perPage = $request->input('per_page', 24);

         // Filtros opcionais (name and type)
         $query = Pokemon::query();
 
         // Filtro por name
         if ($request->has('name')) {
             $query->where('name', 'like', '%' . $request->input('name') . '%');
         }
 
         // Filtro por tipo
         if ($request->has('type')) {
             $query->where('type', 'like', '%' . $request->input('type') . '%');
         }
 
         // Realiza a paginação com os filtros aplicados
         $pokemons = $query->paginate($perPage);
 
         // Retorna os resultados em formato JSON
         return response()->json($pokemons);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pokemon $pokemon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pokemon $pokemon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pokemon $pokemon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pokemon $pokemon)
    {
        //
    }
}
