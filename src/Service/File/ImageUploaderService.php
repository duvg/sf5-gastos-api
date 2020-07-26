<?php


namespace App\Service\File;


use App\Entity\User;
use App\Repository\UserRepository;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageUploaderService
{
    private const INPUT_NAME = 'avatar';
    private const VALID_MIME_TYPES = ['image/png', 'image/jpeg', 'image/jpg'];

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var FilesystemInterface
     */
    private FilesystemInterface $defaultStorage;
    private string $mediaPath;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        UserRepository $userRepository,
        FilesystemInterface $defaultStorage,
        string $mediaPath,
        LoggerInterface $logger
    )
    {
        $this->userRepository = $userRepository;
        $this->defaultStorage = $defaultStorage;
        $this->mediaPath = $mediaPath;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function uploadAvatar(Request $request, User $user): User
    {
        if (null === $file = $request->files->get(self::INPUT_NAME)) {
            throw new BadRequestHttpException(\sprintf('%s input file not found', self::INPUT_NAME));
        }

        if (!\in_array($file->getMimeType(), self::VALID_MIME_TYPES, true)) {
            throw new BadRequestHttpException('File type not supported');
        }

        $filename = \sprintf('%s.%s', Uuid::uuid4()->toString(), $file->guessExtension());

        $this->defaultStorage->writeStream(
            $filename,
            \fopen($file->getPathname(), 'r'),
            ['visibility' => AdapterInterface::VISIBILITY_PUBLIC]
        );

        $user->setAvatar(\sprintf('%s%s', $this->mediaPath,  $filename));

        $this->userRepository->save($user);

        return  $user;
    }
}