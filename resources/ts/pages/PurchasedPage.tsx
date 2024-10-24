import * as React from 'react'
import { useEffect } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router-dom'
import { useGetUserQuery } from '../store/api/authApi.js'

export default function PurchasedPage() {
  const { data, isLoading } = useGetUserQuery()

  const navigate = useNavigate()
  const [params] = useSearchParams({ sale_id: undefined })

  useEffect(() => {
    if (data && 'email' in data) {
      navigate('/dashboard')
    }
  }, [data])

  return (
    <>
      {isLoading ? (
        'loading'
      ) : (
        <div className="container">
          <h1>
            Thank you for your purchase, you can now start using Scrybble.
          </h1>
          <p>Log in to your account to get started</p>
          <div className="flex">
            <Link
              className="btn btn-primary"
              to={`/auth/register?${params.toString()}`}
            >
              Sign up
            </Link>
            &nbsp;or&nbsp;
            <Link
              className="btn btn-primary"
              to={`/auth/login?${params.toString()}`}
            >
              Log in
            </Link>
          </div>
        </div>
      )}
    </>
  )
}
