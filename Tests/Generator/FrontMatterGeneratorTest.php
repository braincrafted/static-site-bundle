<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Tests\Generator;

use Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * FrontMatterGeneratorTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class FrontMatterGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::__construct()
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::getDirectoryName()
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::getParameters()
     */
    public function constructorShouldSetFilenameAndParameter()
    {
        $generator = new FrontMatterGenerator([ 'directory_name' => 'files', 'parameters' => [ 'foo' ] ]);
        $this->assertEquals('files', $generator->getDirectoryName());
        $this->assertEquals([ 'foo' ], $generator->getParameters());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoDirectoryName()
    {
        new FrontMatterGenerator([ ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::generate()
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::getFrontMatter()
     */
    public function generateShouldReturnListOfParameters()
    {
        $root = vfsStreamWrapper::getRoot();
        $dir = new vfsStreamDirectory('data');
        $root->addChild($dir);
        $file1 = vfsStream::newFile('file1.txt');
        $file1->setContent("---\na: param1a\nb: param1b\n---\nbla");
        $file2 = vfsStream::newFile('file2.txt');
        $file2->setContent("---\na: param2a\nb: param2b\n---\nfoo");
        $dir->addChild($file1);
        $dir->addChild($file2);

        $generator = new FrontMatterGenerator([ 'directory_name' => $dir->url() ]);

        $parameters = $generator->generate();

        $this->assertCount(2, $parameters);
        $this->assertCount(2, $parameters[0]);
        $this->assertCount(2, $parameters[1]);
        $this->assertEquals('param1a', $parameters[0]['a']);
        $this->assertEquals('param1b', $parameters[0]['b']);
        $this->assertEquals('param2a', $parameters[1]['a']);
        $this->assertEquals('param2b', $parameters[1]['b']);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::generate()
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::getFrontMatter()
     */
    public function generateShouldReturnListOfParametersThatAreConfigured()
    {
        $root = vfsStreamWrapper::getRoot();
        $dir = new vfsStreamDirectory('data');
        $root->addChild($dir);
        $file1 = vfsStream::newFile('file1.txt');
        $file1->setContent("---\na: param1a\nb: param1b\n---\nbla");
        $file2 = vfsStream::newFile('file2.txt');
        $file2->setContent("---\na: param2a\nb: param2b\n---\nfoo");
        $dir->addChild($file1);
        $dir->addChild($file2);

        $generator = new FrontMatterGenerator([ 'directory_name' => $dir->url(), 'parameters' => [ 'a' ] ]);

        $parameters = $generator->generate();

        $this->assertCount(2, $parameters);
        $this->assertCount(1, $parameters[0]);
        $this->assertCount(1, $parameters[1]);
        $this->assertEquals('param1a', $parameters[0]['a']);
        $this->assertFalse(isset($parameters[0]['b']));
        $this->assertEquals('param2a', $parameters[1]['a']);
        $this->assertFalse(isset($parameters[1]['b']));
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FrontMatterGenerator::generate()
     *
     * @expectedException Cocur\Bundle\BuildBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new FrontMatterGenerator(
            ['directory_name' => vfsStream::url('data/invalid') ]
        );

        $generator->generate();
    }
}
