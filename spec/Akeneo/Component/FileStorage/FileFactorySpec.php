<?php

namespace spec\Akeneo\Component\FileStorage;

use Akeneo\Component\FileStorage\PathGeneratorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileFactorySpec extends ObjectBehavior
{
    function let(PathGeneratorInterface $pathGenerator)
    {
        $this->beConstructedWith($pathGenerator, '\Akeneo\Component\FileStorage\Model\File');
    }

    function it_creates_a_file_from_a_raw_file($pathGenerator)
    {
        $rawFile = new \SplFileInfo(__FILE__);

        $pathGenerator->generate($rawFile)->willReturn([
            'uuid'      => '12345',
            'file_name' => '12345_my_file.php',
            'path'      => '1/2/3/4/',
            'path_name' => '1/2/3/4/12345_my_file.php',
        ]);
        $file = $this->createFromRawFile($rawFile, 'destination');
        $file->shouldBeValidFile();
    }

    function it_creates_a_file_from_an_uploaded_file($pathGenerator)
    {
        $rawFile = new UploadedFile(__FILE__, 'FileFactorySpec.php', 'text/x-php', filesize(__FILE__));

        $pathGenerator->generate($rawFile)->willReturn([
            'uuid'      => '12345',
            'file_name' => '12345_my_file.php',
            'path'      => '1/2/3/4/',
            'path_name' => '1/2/3/4/12345_my_file.php',
        ]);
        $file = $this->createFromRawFile($rawFile, 'destination');
        $file->shouldBeValidFile();
    }

    public function getMatchers()
    {
        return [
            'beValidFile' => function ($subject) {
                return
                    $subject->getUuid() === '12345' &&
                    $subject->getKey() === '1/2/3/4/12345_my_file.php' &&
                    $subject->getOriginalFilename() === 'FileFactorySpec.php' &&
                    $subject->getMimeType() === 'text/x-php' &&
                    $subject->getSize() === filesize(__FILE__) &&
                    $subject->getExtension() === 'php' &&
                    $subject->getStorage() === 'destination' &&
                    $subject->getHash() === sha1_file(__FILE__);
            }
        ];
    }
}
