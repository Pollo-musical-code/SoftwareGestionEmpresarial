    public function destroy(Venta $venta, VentaService $service)
    {
        try {
            $service->anular($venta);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('ventas.index')
            ->with('success', "Venta «{$venta->numero_factura}» anulada y fichas devueltas al stock.");
    }