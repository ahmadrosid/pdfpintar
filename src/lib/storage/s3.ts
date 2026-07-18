import { CopyObjectCommand, GetObjectCommand, PutObjectCommand, S3Client } from '@aws-sdk/client-s3'
import { getSignedUrl } from '@aws-sdk/s3-request-presigner'

let s3: S3Client | undefined
let s3Public: S3Client | undefined

function getBucket() {
  const bucket = process.env.AWS_BUCKET
  if (!bucket) {
    throw new Error('AWS_BUCKET is not set')
  }
  return bucket
}

function createS3Client(endpoint?: string) {
  return new S3Client({
    region: process.env.AWS_DEFAULT_REGION ?? 'us-east-1',
    endpoint,
    forcePathStyle: process.env.AWS_USE_PATH_STYLE_ENDPOINT === 'true',
    credentials:
      process.env.AWS_ACCESS_KEY_ID && process.env.AWS_SECRET_ACCESS_KEY
        ? {
            accessKeyId: process.env.AWS_ACCESS_KEY_ID,
            secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY,
          }
        : undefined,
  })
}

export function getS3Client() {
  if (!s3) {
    const endpoint = process.env.AWS_S3_ENDPOINT ?? process.env.AWS_ENDPOINT
    s3 = createS3Client(endpoint)
  }
  return s3
}

function getS3PublicClient() {
  if (!s3Public) {
    const endpoint =
      process.env.AWS_S3_PUBLIC_ENDPOINT ??
      process.env.AWS_S3_ENDPOINT ??
      process.env.AWS_ENDPOINT
    s3Public = createS3Client(endpoint)
  }
  return s3Public
}

export async function uploadPrivatePdf(key: string, body: Uint8Array) {
  await getS3Client().send(
    new PutObjectCommand({
      Bucket: getBucket(),
      Key: key,
      Body: body,
      ContentType: 'application/pdf',
    }),
  )
  return key
}

export async function getPrivatePdfBytes(key: string) {
  const response = await getS3Client().send(
    new GetObjectCommand({
      Bucket: getBucket(),
      Key: key,
    }),
  )

  const bytes = await response.Body?.transformToByteArray()
  if (!bytes) {
    throw new Error('Failed to download PDF from storage')
  }

  return bytes
}

export function buildDocumentObjectKey(filename: string) {
  const safeName = filename.replace(/[^\w.\-() ]+/g, '_')
  return `documents/${Date.now()}_${safeName}`
}

export async function getPresignedPdfUrl(
  key: string,
  expiresInSeconds = 300,
) {
  const command = new GetObjectCommand({
    Bucket: getBucket(),
    Key: key,
  })

  return getSignedUrl(getS3PublicClient(), command, { expiresIn: expiresInSeconds })
}

export async function copyPrivatePdf(sourceKey: string, destinationKey: string) {
  await getS3Client().send(
    new CopyObjectCommand({
      Bucket: getBucket(),
      CopySource: `${getBucket()}/${sourceKey}`,
      Key: destinationKey,
      ContentType: 'application/pdf',
    }),
  )

  return destinationKey
}
