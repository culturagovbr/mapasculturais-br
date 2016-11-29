<?php
namespace MapasBR;
use BaseMinc;
use MapasCulturais\App;

class Theme extends BaseMinc\Theme{

    protected static function _getTexts(){
        return array(
            'site: in the region' => 'no Brasil',
            'site: of the region' => 'do Brasil',
            'site: owner' => 'Ministério da Cultura',
            'site: by the site owner' => 'pelo Ministério da Cultura',

            'home: title' => "Bem-vindo ao Mapa da Cultura ",
            'home: abbreviation' => "MinC",
            'home: colabore' => "Participe!",
            'home: welcome' => "<p>O Mapa da Cultura é um espaço para integrar e dar visibilidade para projetos, artistas, espaços, eventos culturais e seus produtores. Ele é a principal base de informações e indicadores do Ministério da Cultura, agregando cadastros de diferentes programas e ações. </p>
             <p> Neste mapa estão reunidas informações do antigo Sistema Nacional de Informações e Indicadores Culturais (SNIIC) e da <a href='http://culturaviva.gov.br'>Rede Cultura Viva</a>. As próximas bases de dados a compor o Mapas da Cultura Brasileira serão o Sistema Nacional de Bibliotecas e o Cadastro Nacional de Museus. </p>
             <p> Além disso, o Ministério da Cultura irá unir esforços com o sistemas de informação de estados e de municípios e, mais do que isso, irá auxiliá-los na consolidação de seus sistemas de informações e indicadores culturais. </p>",
            'home: events' => "Pesquise e encontre eventos culturais em todo Brasil! Uma vez cadastrado no Mapas Culturais, você pode incluir seus eventos na plataforma e divulgá-los gratuitamente. Em breve estes eventos serão difundidos também em aplicativos de celular e sites de agenda cultural!",
            'home: agents' => "Colabore com a gestão da cultura com informações sobre suas iniciativas, preenchendo seu perfil de agente cultural. Neste espaço, estão registrados artistas, gestores, produtores e instituições; uma rede de atores envolvidos na cena cultural brasileira. É possível cadastrar um ou mais agentes (grupos, coletivos, bandas, instituições, empresas, etc.), além de associar ao seu perfil eventos e espaços culturais com divulgação gratuita.",
            'home: spaces' => "Procure e encontre espaços culturais cadastrados na plataforma, acessando os campos de busca combinada que ajudam a refinar sua pesquisa. Cadastre também os espaços onde desenvolve suas atividades artísticas e culturais.",
            'home: projects' => "Os projetos agregam eventos continuados, projetos culturais ou um grupo de eventos. Neste espaço, você encontra leis de fomento, mostras, convocatórias e editais criados, além de diversas iniciativas cadastradas pelos participantes da plataforma. Cadastre-se e divulgue seus projetos.",
            'home: home_devs' => 'Colabore com o Mapas Culturais! Confira a nossa <a href="https://github.com/culturagovbr/mapasculturais-br/blob/master/doc/api.md" target="_blank">API</a>. A partir dela você pode acessar os dados públicos no nosso banco de dados e utilizá-los para desenvolver aplicações externas e derivadas. Além disso, o Mapas Culturais é construído a partir do sofware livre <a href="http://institutotim.org.br/project/mapas-culturais/" target="_blank">Mapas Culturais</a>, fruto de parceria entre o <a href="http://institutotim.org.br" target="_blank">Instituto TIM</a> e a prefeitura de São Paulo, mas já adotado em muitas outras regiões do país. Você pode contribuir para o seu desenvolvimento por meio do <a href="https://github.com/culturagovbr/mapasculturais-br/" target="_blank">GitHub</a>.',
//
//            'search: verified results' => 'Resultados Verificados',
//            'search: verified' => "Verificados"
        );
    }

    static function getThemeFolder() {
        return __DIR__;
    }


    public function _init() {
        parent::_init();

        $app = App::i();

        $this->jsObject['infoboxFields'] .= ',num_sniic';

        $app->hook('template(site.search.<<agent|space|event>>-infobox-new-fields-before):begin', function() use($app) {
            $this->part('infobox-new-fields-before');
          });

        $app->hook('template(panel.<<agents|spaces|events|projects>>.panel-new-fields-before):begin', function($entity) use($app) {
            $this->part('panel-new-fields-before', [ 'entity' => $entity ]);
          });

        $app->hook('controller(panel).extraFields(<<space|project|event>>)', function(&$fields) {
            $fields[] = 'num_sniic';
          });
    }

    public function getMetadataPrefix() {
        return '';
    }


    protected function _getAgentMetadata() {
        return [];
    }

    protected function _getSpaceMetadata() {
        return [];
    }

    protected function _getEventMetadata() {
        return [];
    }

    protected function _getProjectMetadata() {
        return [];
    }

    protected function _getFilters(){
        $en_estado_filter = [
            'fieldType' => 'checklist',
            'label' => 'Estado',
            'placeholder' => 'Selecione os Estados',
            'filter' => [
                'param' => 'En_Estado',
                'value' => 'IN({val})'
            ]
        ];

        $en_municipio_filter = [
            'fieldType' => 'text',
            'label' => 'Município',
            'isInline' => false,
            'isArray' => false,
            'placeholder' => 'Selecione os Municípios',
            'filter' => [
                'param' => 'En_Municipio',
                'value' => 'ILIKE(*{val}*)'
            ]
        ];

        $ent_filters = parent::_getFilters();
        $mod_filters = [];
        foreach ($ent_filters as $entity => $filters) {
            $mod_filters[$entity] = [];
            if (in_array($entity, ['space', 'agent'])){
                $mod_filters[$entity][] = $en_estado_filter;
                $mod_filters[$entity][] = $en_municipio_filter;
            }
            foreach ($filters as $filter)
                if (!(isset($filter['fieldType']) && $filter['fieldType'] === 'checkbox-verified'))
                    $mod_filters[$entity][] = $filter;
        }

        return $mod_filters;
    }
}