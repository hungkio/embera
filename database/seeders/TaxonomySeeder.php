<?php
namespace Database\Seeders;

use App\Domain\Taxonomy\Models\Taxon;
use App\Domain\Taxonomy\Models\Taxonomy;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxonomy = Taxonomy::create(['name' => 'Danh mục']);
        $firstTaxon = Taxon::create(['name' => $taxonomy->name, 'taxonomy_id' => $taxonomy->id]);
        $taxonList = [
            ['name' => 'Danh mục 1', 'childs' => []],
            [
                'name' => 'Danh mục 1',
                'childs' => [
                    [
                        'name' => 'Danh mục 2',
                        'childs' => [
                            ['name' => 'Danh mục 3', 'childs' => []],
                            ['name' => 'Danh mục 3', 'childs' => []],
                        ],
                    ],
                    [
                        'name' => 'Danh mục 2',
                        'childs' => [
                            ['name' => 'Danh mục 3', 'childs' => []],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Danh mục 1',
                'childs' => [
                    ['name' => 'Danh mục 2', 'childs' => []],
                ],
            ],
        ];
        foreach ($taxonList as $data) {
            $root = Taxon::create(['name' => $data['name'], 'taxonomy_id' => $firstTaxon->taxonomy_id, 'parent_id' => $firstTaxon->id]);
            $this->createChild($root, $data['childs'], $firstTaxon);
        }
    }

    private function createChild($root, $childs, $firstTaxon)
    {
        if (count($childs) > 0) {
            foreach ($childs as $child) {
                $newRoot = Taxon::create(['name' => $child['name'], 'taxonomy_id' => $firstTaxon->taxonomy_id, 'parent_id' => $root->id]);
                $this->createChild($newRoot, $child['childs'], $firstTaxon);
            }
        }
    }
}
