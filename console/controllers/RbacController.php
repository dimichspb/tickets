<?php
namespace console\Controllers;

use Yii;
use yii\rbac\Permission;
use yii\rbac\Role;

class RbacController extends \yii\console\Controller
{

    const ADMIN_ROLE_NAME = 'Admin';
    const USER_ROLE_NAME = 'User';

    protected $userRole;
    protected $adminRole;

    /**
     * Action adds all necessary roles and permissions
     *
     */
    public function actionInit()
    {
        $this->actionAddCommonRoles();
        $this->actionAddRequestsPermissions();
        $this->actionAddRatesPermissions();
        $this->actionAddPlacesPermissions();
    }

    /**
     * Action adds common roles - User and Admin
     *
     */
    private function actionAddCommonRoles()
    {
        $this->addCommonUserRole();
        $this->addCommonAdminRole();
    }

    /**
     * Method adds common User role with the specified permissions
     *
     * @param array $permissions
     */
    private function addCommonUserRole(array $permissions = [])
    {
        $auth = Yii::$app->authManager;

        //User role
        if (!$this->userRole = $auth->getRole(self::USER_ROLE_NAME)) {
            $this->userRole = $this->createRole(self::USER_ROLE_NAME, $permissions, 'User role');
        }
    }

    /**
     * Method adds common Admin role with the specified permissions
     *
     * @param array $permissions
     */
    private function addCommonAdminRole(array $permissions = [])
    {
        $auth = Yii::$app->authManager;

        //Admin role
        if (!$this->adminRole = $auth->getRole(self::ADMIN_ROLE_NAME)) {
            $this->adminRole = $this->createRole(self::ADMIN_ROLE_NAME, $permissions, 'Admin role' );
            $auth->assign($this->adminRole, 1); // User with ID 1 has Admin role;
        }
    }

    /**
     * Action adds Requests roles and permissions
     *
     */
    public function actionAddRequestsPermissions()
    {
        //getRequestsList permission
        $getRequestsList = $this->createPermission('getRequestsList', 'Get Requests list permission');

        //getRequestDetails permission
        $getRequestDetails = $this->createPermission('getRequestDetails', 'Get Request details permission');

        //createRequest permission
        $createRequestDetails = $this->createPermission('createRequestDetails', 'Create Request details permission');

        //updateRequest permission
        $updateRequestDetails = $this->createPermission('updateRequestDetails', 'Update Request details permission');

        //deleteRequest permission
        $deleteRequestDetails = $this->createPermission('deleteRequestDetails', 'Delete Request details permission');

        //requestsUser role
        $requestsUser = $this->createRole('requestsUser', [
            $getRequestDetails,
            $createRequestDetails,
            ], 'Requests user role');

        //requestsAdmin role
        $requestsAdmin = $this->createRole('requestsAdmin', [
            $getRequestsList,
            $updateRequestDetails,
            $deleteRequestDetails,
            ], 'Requests admin role');

        $this->addRoleChild($requestsAdmin, $requestsUser);

        //User role
        $this->addCommonUserRole();
        $this->addRoleChild($this->userRole, $requestsUser);

        //Admin role
        $this->addCommonAdminRole();
        $this->addRoleChild($this->adminRole, $requestsAdmin);
    }

    /**
     * Action adds Rates roles and permissions
     *
     */
    public function actionAddRatesPermissions()
    {
        //getRatesList permission
        $getRatesList = $this->createPermission('getRatesList', 'Get Rates list permission');

        //getRateDetails permission
        $getRateDetails = $this->createPermission('getRateDetails', 'Get Rate details permission');

        //createRate permission
        $createRateDetails = $this->createPermission('createRateDetails', 'Create Rate details permission');

        //updateRate permission
        $updateRateDetails = $this->createPermission('updateRateDetails', 'Update Rate details permission');

        //deleteRate permission
        $deleteRateDetails = $this->createPermission('deleteRateDetails', 'Delete Rate details permission');

        //ratesUser role
        $ratesUser = $this->createRole('ratesUser', [
            $getRateDetails,
        ], 'Rates user role');

        //ratesAdmin role
        $ratesAdmin = $this->createRole('ratesAdmin', [
            $getRatesList,
            $createRateDetails,
            $updateRateDetails,
            $deleteRateDetails,
        ], 'Rates admin role');

        $this->addRoleChild($ratesAdmin, $ratesUser);

        //User role
        $this->addCommonUserRole();
        $this->addRoleChild($this->userRole, $ratesUser);

        //Admin role
        $this->addCommonAdminRole();
        $this->addRoleChild($this->adminRole, $ratesAdmin);
    }

    /**
     * Action adds Places roles and permissions
     *
     */
    public function actionAddPlacesPermissions()
    {
        //getPlacesList permission
        $getPlacesList = $this->createPermission('getPlacesList', 'Get Places list permission');

        //getPlaceDetails permission
        $getPlaceDetails = $this->createPermission('getPlaceDetails', 'Get Place details permission');

        //createPlace permission
        $createPlaceDetails = $this->createPermission('createPlaceDetails', 'Create Place details permission');

        //updatePlace permission
        $updatePlaceDetails = $this->createPermission('updatePlaceDetails', 'Update Place details permission');

        //deletePlace permission
        $deletePlaceDetails = $this->createPermission('deletePlaceDetails', 'Delete Place details permission');

        //placesUser role
        $placesUser = $this->createRole('placesUser', [
            $getPlaceDetails,
            $getPlacesList,
        ], 'Places user role');

        //placesAdmin role
        $placesAdmin = $this->createRole('placesAdmin', [
            $createPlaceDetails,
            $updatePlaceDetails,
            $deletePlaceDetails,
        ], 'Places admin role');

        $this->addRoleChild($placesAdmin, $placesUser);

        //User role
        $this->addCommonUserRole();
        $this->addRoleChild($this->userRole, $placesUser);

        //Admin role
        $this->addCommonAdminRole();
        $this->addRoleChild($this->adminRole, $placesAdmin);
    }

    /**
     * Method creates new Permission
     *
     * @param $permissionName
     * @param string $permissionDesc
     * @return null|Permission
     */
    private function createPermission($permissionName, $permissionDesc = '')
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission($permissionName)) {

        } else {
            $permissionDesc = empty($permissionDesc) ? $permissionName : $permissionDesc;

            $permission = $auth->createPermission($permissionName);
            $permission->description = $permissionDesc;
            if ($auth->add($permission)) {
                $this->stdout($permission->description . " has been added\n");
            }
        }
        return $permission;
    }

    /**
     * Method creates new Role with the specified $permissions
     *
     * @param $roleName
     * @param array $permissions
     * @param string $roleDesc
     * @return null|Role
     */
    private function createRole($roleName, array $permissions, $roleDesc = '')
    {
        $auth = Yii::$app->authManager;

        if ($role = $auth->getRole($roleName)) {
            $this->addRolePermissions($role, $permissions);
        } else {
            $roleDesc = empty($roleDesc) ? $roleName : $roleDesc;

            $role = $auth->createRole($roleName);
            $role->description = $roleDesc;
            if ($auth->add($role)) {
                $this->stdout($role->description . " has been added\n");
                $this->addRolePermissions($role, $permissions);
            }
        }
        return $role;
    }

    /**
     * Method adds specified $permissions to the specified $role
     *
     * @param Role $role
     * @param array $permissions
     */
    private function addRolePermissions(Role $role, array $permissions)
    {
        $auth = Yii::$app->authManager;

        foreach ($permissions as $permission) {
            if (!$this->checkRolePermission($role, $permission)) {
                $auth->addChild($role, $permission);
            }
        }
    }

    /**
     * Method adds specified child to the specified $role
     *
     * @param Role $role
     * @param $child
     */
    private function addRoleChild(Role $role, $child)
    {
        $auth = Yii::$app->authManager;

        if (!$this->checkRoleChild($role, $child)) {
            $auth->addChild($role, $child);
        }

    }

    /**
     * Method check whether specified $role has specified $permissions
     *
     * @param Role $role
     * @param Permission $permission
     * @return bool
     */
    private function checkRolePermission(Role $role, Permission $permission)
    {
        $auth = Yii::$app->authManager;

        $rolePermissions = $auth->getPermissionsByRole($role->name);

        return in_array($permission, $rolePermissions);
    }

    /**
     * Method check whether specified $role has specified $child
     *
     * @param Role $role
     * @param $child
     * @return bool
     */
    private function checkRoleChild(Role $role, $child)
    {
        $auth = Yii::$app->authManager;

        $roleChildren = $auth->getChildren($role->name);

        return in_array($child, $roleChildren);
    }

    /**
     * Method removes all Roles and Permissions
     */
    public function actionRemoveAll()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $this->stdout("All permissions and roles have been removed\n");
    }
}