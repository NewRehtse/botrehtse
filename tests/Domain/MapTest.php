<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Tests\Domain;

use App\Domain\Lighthouse;
use App\Domain\Map;
use App\Domain\Position;
use App\Domain\Square;
use PHPUnit\Framework\TestCase;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 *
 * @covers \App\Domain\Map
 */
class MapTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider getDataForInitialize
     *
     * @param array $data
     */
    public function shouldBuildMapFromArray(array $data): void
    {
        $map = Map::buildFromArray($data);

        $view = $map->view();

        /** @var Square $square00 */
        $square00 = $view[0][0];
        /** @var Square $square11 */
        $square11 = $view[1][1];
        /** @var Square $square23 */
        $square23 = $view[2][3];
        /** @var Square $square44 */
        $square44 = $view[4][4];

        static::assertTrue($square00->isOutsideBoard());
        static::assertTrue($square44->isOutsideBoard());
        static::assertFalse($square11->isOutsideBoard());
        static::assertFalse($square23->isOutsideBoard());
        static::assertInstanceOf(Lighthouse::class, $square11->lighthouse());
        static::assertInstanceOf(Lighthouse::class, $square23->lighthouse());

        static::assertCount(4, $map->lighthouses());
        static::assertNull($map->positionMaxEnergy());
    }

    /**
     * @test
     */
    public function shouldTestEnergyFromMatrixFunctions(): void
    {
        $functions = Map::energyFromMatrixFunctions();

        $points = $functions[0][0](30, 30);
        static::assertEquals(27, $points[0]);
        static::assertEquals(27, $points[1]);
    }

    /**
     * @test
     *
     * @dataProvider getDataForUpdaten
     *
     * @param array $creationData
     * @param array $updateData
     */
    public function shouldUpdateView(array $creationData, array $updateData): void
    {
        $map = Map::buildFromArray($creationData);

        $position = Position::buildFromArray($updateData['position']);
        $energyMatrix = $updateData['view'];

        $map->updateView($position, $energyMatrix);

        $view = $map->view();
        /** @var Square $square11 */
        $square11 = $view[1][1];
        /** @var Square $square12 */
        $square12 = $view[1][2];
        /** @var Square $square23 */
        $square23 = $view[2][3];

        static::assertEquals(50, $square11->energy());
        static::assertEquals(32, $square12->energy());
        static::assertEquals(50, $square23->energy());

        $position = $square23->position();
        static::assertEquals($position, $map->positionMaxEnergy());
    }

    /**
     * @test
     *
     * @dataProvider getDataForUpdaten
     *
     * @param array $creationData
     * @param array $udpateData
     */
    public function shouldUpdateLighthouses(array $creationData, array $udpateData): void
    {
        $map = Map::buildFromArray($creationData);

        $map->updateLighthouses($udpateData['lighthouses']);

        $view = $map->view();

        /** @var Square $square11 */
        $square11 = $view[1][1];
        $lighthouse11 = $square11->lighthouse();
        static::assertNotNull($lighthouse11);
        static::assertEquals(0, $lighthouse11->owner());
        static::assertEquals(30, $lighthouse11->energy());
        static::assertNotEmpty($lighthouse11->connections());
    }

    /**
     * @return array
     */
    public function getDataForInitialize(): array
    {
        return [
            [
                \json_decode(\file_get_contents(__DIR__.'/../JSON/initialization.json'), true),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getDataForUpdaten(): array
    {
        return [
            [
                \json_decode(\file_get_contents(__DIR__.'/../JSON/initialization.json'), true),
                \json_decode(\file_get_contents(__DIR__.'/../JSON/update.json'), true),
            ],
        ];
    }
}
