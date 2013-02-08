<?php
/**
 * Arquivo que traz a classe de Paginação
 *
 * PHP Version 5.3
 *
 * @category Classes
 * @package  Pagination
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */

/**
 * Classe de Paginação
 *
 * @category Classes
 * @package  Pagination
 * @author   Espiral Interativa <ti@espiralinterativa.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://espir.al
 */
class Pagination
{
    /**
     * @var Integer
     * @access protected
     */
    protected $total_itens;
    /**
     * @var Integer
     * @access protected
     */
    protected $total_paginas;
    /**
     * @var Integer
     * @access protected
     */
    protected $pagina_atual;
    /**
     * @var Integer
     * @access protected
     */
    protected $quantidade_itens;
    /**
     * @var String
     * @access protected
     */
    protected $parametro_adicional;

    /**
     * Função que constrói o objeto
     *
     * @param Integer $itens     {Número de itens a serem paginados}
     * @param Integer $pag_atual {Página atual}
     * @param Integer $qtd_itens {Número de itens por página}
     *
     * @return void
     * @access public
     */
    public function __construct($itens, $pag_atual, $qtd_itens)
    {
        $this->total_itens      = (is_array($itens) ? count($itens) : (int) $itens);
        $this->pagina_atual     = (int)$pag_atual;
        $this->quantidade_itens = (int)$qtd_itens;

        $this->total_paginas = (int) ($this->total_itens / $this->quantidade_itens);
        if ($this->total_itens % $this->quantidade_itens > 0) {
            $this->total_paginas = $this->total_paginas + 1;
        }
        $this->_setAditionalParam();
    }


    /**
     * Define os parametros GET adicionais na url (ex: &cidade=jundiai)
     *
     * @return void
     * @access private
     */
    private function _setAditionalParam()
    {
        $paramOk = array();
        $param   = explode('&', $_SERVER['QUERY_STRING']);
        foreach ($param as $value) {
            $ret = strpos($value, 'pag=');
            if ($ret === false) {
                $paramOk[] = $value;
            }
        }
        $this->parametro_adicional = implode('&', $paramOk);
    }


    /**
     * Retorna o parametro GET adicional
     *
     * @return String
     * @access private
     */
    private function _getAditionalParam()
    {
        return $this->parametro_adicional;
    }


    /**
     * Retorna o código html com as páginas
     *
     * @return String
     * @access public
     */
    public function getHtmlPaginas()
    {
        global $SPECIAL_CONTENTS;

        $str             = '';
        $limite_esquerda = 5;
        $limite_direita  = 5;

        $mostra_inicio = $this->pagina_atual - $limite_esquerda;
        if ($mostra_inicio <= 0) {
            $mostra_inicio = 1;
        }
        $mostra_fim = $this->pagina_atual + $limite_direita;
        if ($mostra_fim > $this->total_paginas) {
            $mostra_fim = $this->total_paginas;
        }

        if ($mostra_inicio > 1) {
            $str .= '<a href="?pag=' . ($mostra_inicio - 1) .
                    '&amp;' . $this->_getAditionalParam() .
                    '" class="more_pages back-page">' . $i18n->_('Página anterior') . '</a>';
        }
        for ($i = $mostra_inicio; $i <= $mostra_fim; $i++) {
            if ($this->pagina_atual == $i) {
                $str .= '<a href="?pag=' . $i . '&amp;' .
                        $this->_getAditionalParam() .
                        '" class="selected">'.$i.'</a>';
            } else {
                $str .= '<a href="?pag=' . $i . '&amp;' .
                        $this->_getAditionalParam() . '">' . $i . '</a>';
            }
        }
        if ($mostra_fim < $this->total_paginas) {
            $str .= '<a href="?pag=' . ($mostra_fim + 1) . '&amp;'.
                    $this->_getAditionalParam() .
                    '" class="more_pages next-page">' . $i18n->_('Próxima página') . '</a>';
        }

        return $str;
    }


    /**
     * Retorna o código html com as páginas
     *
     * @return String
     * @access public
     */
    public function getHtmlAditionalInfo()
    {
        global $SPECIAL_CONTENTS;

        $str = '';
        $str .= '<div class="paginacao_info">';
        if ($this->total_paginas > 1) {
            if ($this->pagina_atual > 1) {
                $str .= ($this->getInicio()) . '-';

                $qtd = $this->quantidade_itens;

                if (($this->getInicio() + $qtd - 1) > $this->total_itens) {
                    $str .= $this->total_itens;
                } else {
                    $str .= ($this->getInicio() + $this->quantidade_itens - 1);
                }
            } else {
                if ($this->total_paginas <= 1) {
                    if ($this->total_itens = 0) {
                        $str .= ($this->getInicio()).'-'.($this->total_itens);
                    } else {
                        $str .= ($this->getInicio() + 1).'-'.($this->total_itens);
                    }
                } else {
                    $str .= ($this->getInicio() + 1).'-'.
                            ($this->getInicio() + $this->quantidade_itens);
                }
            }

        } else {
            $str .= $i18n->_('Foram encontrados') . ' <strong>' .
                    $this->total_itens .
                    '</strong> ' . $i18n->_('registros') . '.';
        }
        $str .= '</div>';

        return $str;
    }


    /**
     * Retorna o código html detalhado com as páginas
     *
     * @return String
     * @access public
     */
    public function getHtmlFullPaginacao()
    {
        if ($this->getHtmlPaginas() == '') {
            $n_paginas = '-';
        } else {
            $n_paginas = $this->getHtmlPaginas();
        }
        $str = '<div class="tbl_paginacao">'.
               $this->getHtmlAditionalInfo().''.$n_paginas.
               '</div>';
        return $str;
    }


    /**
     * Retorna o número do primeiro item da página
     *
     * @return String
     * @access public
     */
    public function getInicio()
    {
        $str = ($this->pagina_atual*$this->quantidade_itens)-$this->quantidade_itens;
        return $str;
    }


    /**
     * Retorna a quantidade de itens por página
     *
     * @return String
     * @access public
     */
    public function getFim()
    {
        return $this->quantidade_itens;
    }


}
?>