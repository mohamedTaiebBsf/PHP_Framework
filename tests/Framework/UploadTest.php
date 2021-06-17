<?php

namespace Tests\Framework;

use Framework\Upload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadTest extends TestCase
{
    /**
     * @var Upload
     */
    private $upload;

    protected function setUp(): void
    {
        $this->upload = new Upload('tests');
    }

    protected function tearDown(): void
    {
        if (file_exists('tests/demo.jpg')) {
            unlink('tests/demo.jpg');
        }
    }

    public function testUpload()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadedFile->expects($this->any())
            ->method('getError')
            ->willReturn(UPLOAD_ERR_OK);

        $uploadedFile->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('demo.jpg');

        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo.jpg'));

        $this->assertEquals('demo.jpg', $this->upload->upload($uploadedFile));
    }

    public function testUploadWithExistingFile()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadedFile->expects($this->any())
            ->method('getError')
            ->willReturn(UPLOAD_ERR_OK);

        $uploadedFile->expects($this->any())
            ->method('getError')
            ->willReturn(UPLOAD_ERR_OK);

        $uploadedFile->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('demo.jpg');

        touch('tests/demo.jpg');

        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo_copy.jpg'));

        $this->assertEquals('demo_copy.jpg', $this->upload->upload($uploadedFile));
    }

    public function testDontMoveIfFileNotUploaded()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadedFile->expects($this->any())
            ->method('getError')
            ->willReturn(UPLOAD_ERR_CANT_WRITE);

        $uploadedFile->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('demo.jg');

        $uploadedFile->expects($this->never())
            ->method('moveTo')
            ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo.jpg'));

        $this->assertNull($this->upload->upload($uploadedFile));
    }
}