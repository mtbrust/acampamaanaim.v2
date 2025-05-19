<?php

namespace pages;

use desv\controllers\EndPoint;
use desv\controllers\Render;

/**
 * Modelo para página html simples.
 * 
 */
class model_lite extends EndPoint
{

	/**
	 * * *******************************************************************************************
	 * PERSONALIZAÇÃO DO ENDPOINT
	 * * *******************************************************************************************
	 */


	/**
	 * loadParams
	 * Carrega os parâmetros de personalização do endpoint.
	 * Valores Default vem da config.
	 *
	 * @return void
	 */
	public function loadParams(){}


	/**
	 * get
	 * 
	 * Função principal.
	 * Recebe todos os parâmetros do endpoint em $params.
	 *
	 * @param  mixed $params
	 * 
	 * @return mixed
	 */
	public function get($params)
	{
		// Exibe o conteúdo html no corpo da página.
		// self::$params['html'] = "Conteúdo Body";
		// Exibe a lista de parâmetros que a página disponibiliza.
		self::$params['html'] = Render::obj('docs/show_params.html', $params);
	}
}