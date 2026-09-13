<?php

namespace Innoboxrr\Support\Tests\Unit;

use Illuminate\Http\Request;
use Innoboxrr\Support\Http\Requests\RequestFormater;
use Innoboxrr\Support\Tests\TestCase;

/**
 * Lo usan las requests de varios paquetes para convertir grupos anidados de un
 * formulario en metas planas, y no lo cubría nada.
 */
final class RequestFormaterTest extends TestCase
{
    public function test_aplana_varios_niveles_con_guion_bajo(): void
    {
        $this->assertSame(
            ['title' => 'Hola', 'seo_title' => 'T', 'seo_og_image' => 'img.png'],
            RequestFormater::flatten(['title' => 'Hola', 'seo' => ['title' => 'T', 'og' => ['image' => 'img.png']]])
        );
    }

    public function test_conserva_las_listas_para_guardarlas_enteras(): void
    {
        $this->assertSame(
            ['seo_keywords' => ['a', 'b']],
            RequestFormater::flatten(['seo' => ['keywords' => ['a', 'b']]])
        );
    }

    public function test_conserva_las_listas_de_valores_como_correos(): void
    {
        $emails = [['value' => 'a@x.com'], ['value' => 'b@x.com']];

        $this->assertSame(['contact_emails' => $emails], RequestFormater::flatten(['contact' => ['emails' => $emails]]));
    }

    /**
     * Un grupo vacío contaba como arreglo asociativo y desaparecía de la
     * petición: vaciarlo en el formulario no borraba la meta.
     */
    public function test_un_grupo_vacio_llega_vacio_en_lugar_de_desaparecer(): void
    {
        $this->assertSame(['seo' => [], 'title' => 'x'], RequestFormater::flatten(['seo' => [], 'title' => 'x']));
    }

    public function test_los_valores_vacios_se_conservan_para_poder_borrar(): void
    {
        $this->assertSame(['seo_title' => '', 'seo_description' => null], RequestFormater::flatten(['seo' => ['title' => '', 'description' => null]]));
    }

    public function test_si_dos_claves_coinciden_gana_la_primera(): void
    {
        $this->assertSame(['seo_title' => 'plana'], RequestFormater::flatten(['seo_title' => 'plana', 'seo' => ['title' => 'anidada']]));
    }

    public function test_format_sustituye_el_contenido_de_la_peticion(): void
    {
        $request = Request::create('/', 'POST', ['seo' => ['title' => 'T']]);

        RequestFormater::format($request);

        $this->assertSame(['seo_title' => 'T'], $request->all());
    }
}
