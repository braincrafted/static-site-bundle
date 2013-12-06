<?php

/**
 * This file is part of BraincraftedCocurBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\CocurBundle\Tests\Generator;

use Braincrafted\Bundle\CocurBundle\Generator\FileGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * FileGeneratorTest
 *
 * @category   Test
 * @package    BraincraftedCocurBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class FileGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var rg\bovigo\vfs\vfsStreamFile */
    private $file;

    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('data'));
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Generator\FileGenerator::__construct()
     * @covers Braincrafted\Bundle\CocurBundle\Generator\FileGenerator::getFilename()
     * @covers Braincrafted\Bundle\CocurBundle\Generator\FileGenerator::getParameter()
     */
    public function constructorShouldSetFilenameAndParameter()
    {
        $generator = new FileGenerator([ 'filename' => 'file.txt', 'parameter' => 'name' ]);
        $this->assertEquals('file.txt', $generator->getFilename());
        $this->assertEquals('name', $generator->getParameter());
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Generator\FileGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoFilename()
    {
        new FileGenerator([ 'parameter' => 'name' ]);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Generator\FileGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoParameter()
    {
        new FileGenerator([ 'filename' => 'file.txt' ]);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Generator\FileGenerator::generate()
     */
    public function generateShouldReturnListOfParameters()
    {
        $file = vfsStream::newFile('parameters.txt')->at(vfsStreamWrapper::getRoot());
        $generator = new FileGenerator([ 'filename' => vfsStream::url('data/parameters.txt'), 'parameter' => 'var' ]);
        $file->setContent("param1\nparam2\nparam3\n");

        $parameters = $generator->generate();

        $this->assertCount(3, $parameters);
        $this->assertEquals('param1', $parameters[0]['var']);
        $this->assertEquals('param2', $parameters[1]['var']);
        $this->assertEquals('param3', $parameters[2]['var']);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Generator\FileGenerator::generate()
     *
     * @expectedException Braincrafted\Bundle\CocurBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new FileGenerator([ 'filename' => vfsStream::url('data/parameters.txt'), 'parameter' => 'var' ]);

        $generator->generate();
    }
}
