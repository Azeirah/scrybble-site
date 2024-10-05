import React from 'react'
import { useLicenseInformationQuery } from '../../store/api/apiRoot.ts'
import './UserProfile.scss'
import { useGetUserQuery } from '../../store/api/authApi.ts'

export default function UserProfile() {
  const {
    data: licenseData,
    isSuccess: licenseSuccess,
    isLoading: licenseLoading,
  } = useLicenseInformationQuery()
  const { data, isLoading, isSuccess } = useGetUserQuery()

  return (
    <div>
      <h1>Profile</h1>
      {isLoading ? (
        'Loading...'
      ) : (
        <div className="user-info">
          <label htmlFor="name">Name</label>
          <span id="name">{data.name}</span>
          <label htmlFor="Email">E-mail</label>
          <span id="email">{data.email}</span>
        </div>
      )}

      <h2>Your subscription</h2>
      {licenseLoading ? (
        'Loading...'
      ) : (
        <>
          {licenseData?.lifetime ? (
            <span className="badge bg-success">Lifetime license!</span>
          ) : licenseData?.exists ? (
            <>
              {licenseData.licenseInformation.active ? (
                <span className="badge bg-success">Active</span>
              ) : (
                <span className="badge bg-danger">Inactive</span>
              )}
              <div>
                <a
                  href={`https://app.gumroad.com/subscriptions/${licenseData.licenseInformation.subscription_id}/manage`}
                >
                  Manage your subscription
                </a>
              </div>
            </>
          ) : (
            <>Unable to load license information</>
          )}
        </>
      )}
    </div>
  )
}
